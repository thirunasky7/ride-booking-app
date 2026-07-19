<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TimeSlot;
use App\Models\Vehicle;
use App\Services\BookingService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SlotApiController extends Controller
{
    use ApiResponse;

    public function availableSlots(Request $request)
    {
        $request->validate(['booking_date' => 'required|date']);

        $cacheKey = 'available_slots:'.$request->booking_date;

        $slots = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request) {
            $timeSlots = TimeSlot::where('status', 1)->get();
            $vehiclesCount = Vehicle::where('status', 1)->count();
            $response = [];

            foreach ($timeSlots as $slot) {
                $bookedCount = Booking::where('booking_date', $request->booking_date)
                    ->where('slot_time', $slot->slot_time)
                    ->whereIn('status', BookingService::ACTIVE_STATUSES)
                    ->count();

                $available = max(0, $vehiclesCount - $bookedCount);

                $response[] = [
                    'time_slot_id' => $slot->id,
                    'slot_time' => $slot->slot_time,
                    'available_vehicles' => $available,
                    'is_available' => $available > 0,
                ];
            }

            return $response;
        });

        return $this->success(['slots' => $slots]);
    }
}
