<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Apartment;
use App\Models\BusStand;
use App\Models\TimeSlot;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with([
            'customer',
            'vehicle',
            'apartment',
            'busStand'
        ])
        ->latest()
        ->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $customers = User::where('role', 'customer')->get();

        $apartments = Apartment::where('status', 1)->get();

        $busStands = BusStand::where('status', 1)->get();

        $timeSlots = TimeSlot::where('status', 1)->get();

        return view(
            'admin.bookings.create',
            compact(
                'customers',
                'apartments',
                'busStands',
                'timeSlots'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([

            'user_id' => 'required',

            'apartment_id' => 'required',

            'bus_stand_id' => 'required',

            'booking_date' => 'required|date',

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

            $alreadyBooked = Booking::where(
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

            if (!$alreadyBooked) {

                $assignedVehicle = $vehicle;

                break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | No Vehicle Available
        |--------------------------------------------------------------------------
        */

        if (!$assignedVehicle) {

            return back()
                ->withErrors([
                    'slot_time' =>
                    'No Vehicles Available For Selected Slot'
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Create Booking
        |--------------------------------------------------------------------------
        */

        Booking::create([

            'user_id' => $request->user_id,

            'vehicle_id' => $assignedVehicle->id,

            'apartment_id' => $request->apartment_id,

            'bus_stand_id' => $request->bus_stand_id,

            'booking_date' => $request->booking_date,

            'slot_time' => $request->slot_time,

            'trip_type' => $request->trip_type,

            'status' => 'confirmed',

        ]);

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking Created Successfully');
    }

    public function edit(Booking $booking)
    {
        $customers = User::where('role', 'customer')->get();

        $apartments = Apartment::where('status', 1)->get();

        $busStands = BusStand::where('status', 1)->get();

        $timeSlots = TimeSlot::where('status', 1)->get();

        return view(
            'admin.bookings.edit',
            compact(
                'booking',
                'customers',
                'apartments',
                'busStands',
                'timeSlots'
            )
        );
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate([

            'status' => 'required',

        ]);

        $booking->update([

            'status' => $request->status,

        ]);

        return redirect()
            ->route('bookings.index')
            ->with('success', 'Booking Updated Successfully');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return back()
            ->with('success', 'Booking Deleted Successfully');
    }
}