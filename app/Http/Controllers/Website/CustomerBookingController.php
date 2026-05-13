<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apartment;
use App\Models\BusStand;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\TimeSlot;
use App\Models\RoutePrice;

class CustomerBookingController extends Controller
{
    public function dashboard()
    {
        return view(
            'website.customer.dashboard'
        );
    }

    public function create()
    {
        $apartments = Apartment::where(
            'status',
            1
        )->get();

        $busStands = BusStand::where(
            'status',
            1
        )->get();

        $slots = TimeSlot::where(
            'status',
            1
        )->get();

        return view(
            'website.customer.book-ride',
            compact(
                'apartments',
                'busStands',
                'slots'
            )
        );
    }

    public function store(Request $request)
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
        | Find Vehicle
        |--------------------------------------------------------------------------
        */

        $vehicles = Vehicle::where(
            'status',
            1
        )->get();

        $assignedVehicle = null;

        foreach ($vehicles as $vehicle) {

            $exists = Booking::where(
                'vehicle_id',
                $vehicle->id
            )
            ->where(
                'booking_date',
                $request->booking_date
            )
            ->where(
                'slot_time',
                $request->slot_time
            )
            ->whereIn('status', [
                'pending',
                'confirmed',
                'started'
            ])
            ->exists();

            if (!$exists) {

                $assignedVehicle = $vehicle;

                break;
            }
        }

        if (!$assignedVehicle) {

            return back()->withErrors([

                'slot_time' =>
                'No Vehicle Available'

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Price
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
      

        $price = $routePrice?->base_price ?? 0;
        /*
        |--------------------------------------------------------------------------
        | Booking
        |--------------------------------------------------------------------------
        */

        Booking::create([

            'user_id' => auth()->id(),

            'vehicle_id' => $assignedVehicle->id,

            'apartment_id' => $request->apartment_id,

            'bus_stand_id' => $request->bus_stand_id,

            'booking_date' => $request->booking_date,

            'slot_time' => $request->slot_time,

            'trip_type' => $request->trip_type,

            'price' => $price,

            'status' => 'confirmed',

        ]);

        return redirect()
            ->route('customer.myBookings')
            ->with(
                'success',
                'Booking Created Successfully'
            );
    }

    public function myBookings()
    {
        $bookings = Booking::with([
            'vehicle',
            'apartment',
            'busStand'
        ])
        ->where(
            'user_id',
            auth()->id()
        )
        ->latest()
        ->paginate(20);

        return view(
            'website.customer.my-bookings',
            compact('bookings')
        );
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

        return back()->with(
            'success',
            'Booking Cancelled'
        );
    }
}