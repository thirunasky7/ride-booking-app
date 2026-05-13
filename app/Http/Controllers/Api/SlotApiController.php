<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeSlot;
use App\Models\Booking;
use App\Models\Vehicle;

class SlotApiController extends Controller
{
    public function availableSlots(Request $request)
    {
        $request->validate([

            'booking_date' => 'required|date',

        ]);

        $slots = TimeSlot::where(
            'status',
            1
        )->get();

        $vehiclesCount = Vehicle::where(
            'status',
            1
        )->count();

        $response = [];

        foreach ($slots as $slot) {

            $bookedCount = Booking::where(
                'booking_date',
                $request->booking_date
            )
            ->where(
                'slot_time',
                $slot->slot_time
            )
            ->whereIn('status', [
                'pending',
                'confirmed'
            ])
            ->count();

            $available = $vehiclesCount - $bookedCount;

            $response[] = [

                'slot_time' => $slot->slot_time,

                'available_vehicles' => $available,

                'is_available' => $available > 0

            ];
        }

        return response()->json([

            'status' => true,

            'slots' => $response

        ]);
    }
}