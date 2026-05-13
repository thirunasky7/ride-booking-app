<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\Vehicle;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Counts
        |--------------------------------------------------------------------------
        */

        $totalBookings = Booking::count();

        $todayBookings = Booking::whereDate(
            'booking_date',
            today()
        )->count();

        $completedTrips = Booking::where(
            'status',
            'completed'
        )->count();

        $cancelledTrips = Booking::where(
            'status',
            'cancelled'
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Earnings
        |--------------------------------------------------------------------------
        */

        $totalEarnings = Booking::where(
            'status',
            'completed'
        )->sum('price');

        $todayEarnings = Booking::where(
            'status',
            'completed'
        )
        ->whereDate(
            'booking_date',
            today()
        )
        ->sum('price');

        /*
        |--------------------------------------------------------------------------
        | Drivers / Vehicles
        |--------------------------------------------------------------------------
        */

        $onlineDrivers = Driver::where(
            'is_online',
            1
        )->count();

        $totalVehicles = Vehicle::count();

        /*
        |--------------------------------------------------------------------------
        | Monthly Revenue Chart
        |--------------------------------------------------------------------------
        */

        $monthlyRevenue = Booking::select(
            DB::raw('MONTH(booking_date) as month'),
            DB::raw('SUM(price) as total')
        )
        ->where('status', 'completed')
        ->groupBy('month')
        ->pluck('total', 'month');

        /*
        |--------------------------------------------------------------------------
        | Monthly Bookings Chart
        |--------------------------------------------------------------------------
        */

        $monthlyBookings = Booking::select(
            DB::raw('MONTH(booking_date) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('month')
        ->pluck('total', 'month');

        return view(
            'admin.dashboard',
            compact(
                'totalBookings',
                'todayBookings',
                'completedTrips',
                'cancelledTrips',
                'totalEarnings',
                'todayEarnings',
                'onlineDrivers',
                'totalVehicles',
                'monthlyRevenue',
                'monthlyBookings'
            )
        );
    }
}