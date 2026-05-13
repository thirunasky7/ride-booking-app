<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DriverApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Driver Login
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $request->validate([

            'mobile' => 'required',

            'password' => 'required',

        ]);

        $driver = Driver::where(
            'mobile',
            $request->mobile
        )->first();

        if (!$driver) {

            return response()->json([

                'status' => false,

                'message' => 'Driver Not Found'

            ]);
        }

        if (!Hash::check(
            $request->password,
            $driver->password
        )) {

            return response()->json([

                'status' => false,

                'message' => 'Invalid Password'

            ]);
        }

        $token = $driver->createToken(
            'driver-token'
        )->plainTextToken;

        return response()->json([

            'status' => true,

            'message' => 'Login Success',

            'token' => $token,

            'driver' => $driver,

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard(Request $request)
    {
        $driver = auth()->user();

        $todayTrips = Booking::whereHas(
            'vehicle',
            function ($q) use ($driver) {

                $q->where(
                    'driver_id',
                    $driver->id
                );
            }
        )
        ->whereDate(
            'booking_date',
            today()
        )
        ->count();

        $completedTrips = Booking::whereHas(
            'vehicle',
            function ($q) use ($driver) {

                $q->where(
                    'driver_id',
                    $driver->id
                );
            }
        )
        ->where('status', 'completed')
        ->count();

        return response()->json([

            'status' => true,

            'today_trips' => $todayTrips,

            'completed_trips' => $completedTrips,

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Today Trips
    |--------------------------------------------------------------------------
    */

    public function todayTrips()
    {
        $driver = auth()->user();

        $bookings = Booking::with([
            'customer',
            'apartment',
            'busStand'
        ])
        ->whereHas('vehicle', function ($q) use ($driver) {

            $q->where(
                'driver_id',
                $driver->id
            );
        })
        ->whereDate(
            'booking_date',
            today()
        )
        ->orderBy('slot_time')
        ->get();

        return response()->json([

            'status' => true,

            'trips' => $bookings

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Start Trip
    |--------------------------------------------------------------------------
    */

    public function startTrip($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->update([

            'status' => 'started'

        ]);

        return response()->json([

            'status' => true,

            'message' => 'Trip Started'

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Complete Trip
    |--------------------------------------------------------------------------
    */

    public function completeTrip($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->update([

            'status' => 'completed'

        ]);

        return response()->json([

            'status' => true,

            'message' => 'Trip Completed'

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Online / Offline
    |--------------------------------------------------------------------------
    */

    public function toggleOnline(Request $request)
    {
        $driver = auth()->user();

        $driver->update([

            'is_online' => $request->is_online

        ]);

        return response()->json([

            'status' => true,

            'message' => 'Status Updated'

        ]);
    }
}