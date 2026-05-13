<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Vehicle;

class BookingApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Create Booking
    |--------------------------------------------------------------------------
    */

    public function createBooking(Request $request)
    {
        $request->validate([

            'apartment_id' => 'required',

            'bus_stand_id' => 'required',

            'booking_date' => 'required',

            'slot_time' => 'required',

            'trip_type' => 'required',

        ]);

        /*
        |--------------------------------------------------------------------------
        | Find Available Vehicle
        |--------------------------------------------------------------------------
        */

        $vehicles = Vehicle::where('status', 1)->get();

        $assignedVehicle = null;

        foreach ($vehicles as $vehicle) {

            $exists = Booking::where(
                'vehicle_id',
                $vehicle->id
            )
            ->where('booking_date', $request->booking_date)
            ->where('slot_time', $request->slot_time)
            ->whereIn('status', [
                'pending',
                'confirmed'
            ])
            ->exists();

            if (!$exists) {

                $assignedVehicle = $vehicle;

                break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | No Vehicle
        |--------------------------------------------------------------------------
        */

        if (!$assignedVehicle) {

            return response()->json([

                'status' => false,

                'message' =>
                'No Vehicles Available'

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Booking
        |--------------------------------------------------------------------------
        */
        $routePrice = RoutePrice::where(
            'apartment_id',
            $request->apartment_id
        )
        ->where(
            'bus_stand_id',
            $request->bus_stand_id
        )
        ->first();

        if (!$routePrice) {

            return response()->json([

                'status' => false,

                'message' => 'Route Price Not Configured'

            ]);
        }

        $finalPrice = $routePrice->base_price;

        $currentTime = $request->slot_time;
        if (
            $routePrice->peak_from &&
            $routePrice->peak_to
        ) {
            if (
                $currentTime >= $routePrice->peak_from &&
                $currentTime <= $routePrice->peak_to
            ) {

                $finalPrice = $routePrice->peak_price;
            }
        }

        $booking = Booking::create([

            'user_id' => auth()->id(),

            'vehicle_id' => $assignedVehicle->id,

            'apartment_id' => $request->apartment_id,

            'bus_stand_id' => $request->bus_stand_id,

            'booking_date' => $request->booking_date,

            'slot_time' => $request->slot_time,

            'trip_type' => $request->trip_type,

            'status' => 'confirmed',
            'price' => $finalPrice,

        ]);

        return response()->json([

            'status' => true,

            'message' => 'Booking Created',

            'booking' => $booking,

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Booking History
    |--------------------------------------------------------------------------
    */

    public function bookingHistory()
    {
        $bookings = Booking::with([
            'vehicle',
            'apartment',
            'busStand'
        ])
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

        return response()->json([

            'status' => true,

            'bookings' => $bookings

        ]);
    }
    public function calendar()
    {
        $bookings = Booking::with([
            'vehicle',
            'customer'
        ])->get();

        return view(
            'admin.bookings.calendar',
            compact('bookings')
        );
    }

    public function upcomingBookings()
{
    $bookings = Booking::with([
        'vehicle',
        'apartment',
        'busStand'
    ])
    ->where('user_id', auth()->id())
    ->whereIn('status', [
        'pending',
        'confirmed',
        'started'
    ])
    ->latest()
    ->get();

    return response()->json([
        'status' => true,
        'bookings' => $bookings
    ]);
}

public function completedBookings()
{
    $bookings = Booking::with([
        'vehicle',
        'apartment',
        'busStand'
    ])
    ->where('user_id', auth()->id())
    ->where('status', 'completed')
    ->latest()
    ->get();

    return response()->json([
        'status' => true,
        'bookings' => $bookings
    ]);
}

    public function cancelBooking($id)
    {
        $booking = Booking::where(
            'user_id',
            auth()->id()
        )->findOrFail($id);

        $booking->update([
            'status' => 'cancelled'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Booking Cancelled'
        ]);
    }
}