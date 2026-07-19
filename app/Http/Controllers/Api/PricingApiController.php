<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookingService;
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
    ) {}

    public function calculatePrice(Request $request)
    {
        $request->validate([
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
                'trip_type', 'apartment_id', 'bus_stand_id',
                'pickup_address', 'drop_address',
                'pickup_lat', 'pickup_lng', 'drop_lat', 'drop_lng',
                'time_slot_id', 'slot_time',
            ]);

            if (($payload['apartment_id'] ?? '') === 'other') {
                $payload['apartment_id'] = null;
            }

            if (($payload['bus_stand_id'] ?? '') === 'other') {
                $payload['bus_stand_id'] = null;
            }

            $isCustomRoute = ($payload['trip_type'] ?? '') === BookingService::TRIP_OTHERS
                || empty($payload['apartment_id'])
                || empty($payload['bus_stand_id']);

            if ($isCustomRoute) {
                $data = $this->bookingService->resolveLocations($payload);
                $slotTime = $data['slot_time'] ?? $request->slot_time;
                $pricing = $this->pricingService->calculateCustom($slotTime, $request->booking_date);

                return $this->success([
                    'estimated_fare' => $pricing['price'],
                    'booking_type' => $this->bookingService->resolveBookingType($request->booking_date),
                    'breakdown' => $pricing,
                    'route' => [
                        'pickup_address' => $data['pickup_address'],
                        'drop_address' => $data['drop_address'],
                    ],
                ]);
            }

            $data = $this->bookingService->resolveLocations($payload);
            $slotTime = $data['slot_time'] ?? $request->slot_time;

            $pricing = $this->pricingService->calculate(
                (int) $data['apartment_id'],
                (int) $data['bus_stand_id'],
                $slotTime,
                $request->booking_date
            );

            return $this->success([
                'estimated_fare' => $pricing['price'],
                'booking_type' => $this->bookingService->resolveBookingType($request->booking_date),
                'breakdown' => $pricing,
                'route' => [
                    'apartment_id' => $data['apartment_id'],
                    'bus_stand_id' => $data['bus_stand_id'],
                ],
            ]);
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
