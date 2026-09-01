<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use App\Services\BookingService;
use App\Services\LocationResolver;
use App\Services\PricingService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use RuntimeException;

class PricingApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected PricingService $pricingService,
        protected BookingService $bookingService,
        protected LocationResolver $locationResolver,
    ) {}

    public function calculatePrice(Request $request)
    {
        $request->validate([
            'pickup_location' => 'nullable|string',
            'drop_location' => 'nullable|string',
            'trip_type' => 'nullable|in:apartment_to_busstand,busstand_to_apartment,others',
            'apartment_id' => 'nullable',
            'bus_stand_id' => 'nullable',
            'pickup_address' => 'nullable|string|max:500',
            'drop_address' => 'nullable|string|max:500',
            'pickup_lat' => 'nullable|numeric',
            'pickup_lng' => 'nullable|numeric',
            'drop_lat' => 'nullable|numeric',
            'drop_lng' => 'nullable|numeric',
            'time_slot_id' => 'nullable|exists:time_slots,id',
            'slot_time' => 'required_without:time_slot_id|nullable',
            'booking_date' => 'required|date|after_or_equal:today',
        ]);

        try {
            $payload = $request->only([
                'pickup_location', 'drop_location',
                'trip_type', 'apartment_id', 'bus_stand_id',
                'pickup_address', 'drop_address',
                'pickup_lat', 'pickup_lng', 'drop_lat', 'drop_lng',
                'time_slot_id', 'slot_time',
            ]);

            if (!empty($payload['pickup_location']) && !empty($payload['drop_location'])) {
                $payload = $this->locationResolver->resolveBookingLocations($payload);
            } elseif (($payload['apartment_id'] ?? '') === 'other') {
                $payload['apartment_id'] = null;
            }

            if (!empty($payload['time_slot_id']) && empty($payload['slot_time'])) {
                $slot = TimeSlot::find($payload['time_slot_id']);
                if ($slot) {
                    $payload['slot_time'] = $slot->slot_time;
                }
            }

            if (($payload['bus_stand_id'] ?? '') === 'other') {
                $payload['bus_stand_id'] = null;
            }

            $isCustomRoute = ($payload['trip_type'] ?? '') === BookingService::TRIP_OTHERS
                || empty($payload['apartment_id'])
                || empty($payload['bus_stand_id']);

            if ($isCustomRoute) {
                $slotTime = $payload['slot_time'] ?? $request->slot_time;
                $pricing = $this->pricingService->calculateCustom($slotTime, $request->booking_date);

                return $this->success([
                    'estimated_fare' => $pricing['price'],
                    'booking_type' => $this->bookingService->resolveBookingType($request->booking_date),
                    'breakdown' => $pricing,
                    'route' => [
                        'pickup_address' => $payload['pickup_address'] ?? null,
                        'drop_address' => $payload['drop_address'] ?? null,
                    ],
                ]);
            }

            $slotTime = $payload['slot_time'] ?? $request->slot_time;

            $pricing = $this->pricingService->calculate(
                (int) $payload['apartment_id'],
                (int) $payload['bus_stand_id'],
                $slotTime,
                $request->booking_date
            );

            return $this->success([
                'estimated_fare' => $pricing['price'],
                'booking_type' => $this->bookingService->resolveBookingType($request->booking_date),
                'breakdown' => $pricing,
                'route' => [
                    'apartment_id' => $payload['apartment_id'],
                    'bus_stand_id' => $payload['bus_stand_id'],
                ],
            ]);
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
