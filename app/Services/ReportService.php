<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\SubscriptionEnquiry;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function dateRange(?string $from, ?string $to): array
    {
        $start = $from ? Carbon::parse($from)->startOfDay() : now()->subDays(30)->startOfDay();
        $end = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();

        return [$start, $end];
    }

    public function summary(?string $from = null, ?string $to = null): array
    {
        [$start, $end] = $this->dateRange($from, $to);

        $bookingsQuery = Booking::whereBetween('created_at', [$start, $end]);

        $completed = (clone $bookingsQuery)->where('status', 'completed');
        $paid = (clone $bookingsQuery)->where('payment_status', 'paid');

        return [
            'total_bookings' => (clone $bookingsQuery)->count(),
            'completed_trips' => (clone $bookingsQuery)->where('status', 'completed')->count(),
            'cancelled_trips' => (clone $bookingsQuery)->where('status', 'cancelled')->count(),
            'pending_trips' => (clone $bookingsQuery)->whereIn('status', ['pending', 'confirmed', 'started'])->count(),
            'total_revenue' => (float) $completed->sum('price'),
            'total_commission' => (float) $completed->sum('commission_amount'),
            'driver_payouts' => (float) $completed->sum('driver_amount'),
            'razorpay_collected' => (float) $paid->where('payment_method', 'razorpay')->sum('price'),
            'cash_collected' => (float) $paid->where('payment_method', 'cash')->sum('price'),
            'upi_collected' => (float) $paid->where('payment_method', 'upi')->sum('price'),
            'pending_payments' => (clone $bookingsQuery)->whereIn('payment_status', ['pending', 'unpaid'])->count(),
            'pending_payment_amount' => (float) (clone $bookingsQuery)->whereIn('payment_status', ['pending', 'unpaid'])->sum('price'),
            'total_customers' => User::where('role', 'customer')->count(),
            'new_customers' => User::where('role', 'customer')->whereBetween('created_at', [$start, $end])->count(),
            'active_subscriptions' => UserSubscription::where('status', 'active')->where('end_date', '>=', today())->count(),
            'subscription_enquiries' => SubscriptionEnquiry::whereBetween('created_at', [$start, $end])->count(),
            'online_drivers' => Driver::where('is_online', 1)->count(),
            'total_vehicles' => Vehicle::where('status', 1)->count(),
        ];
    }

    public function dailyRevenue(?string $from = null, ?string $to = null)
    {
        [$start, $end] = $this->dateRange($from, $to);

        return Booking::select(
            DB::raw('DATE(booking_date) as date'),
            DB::raw('SUM(price) as revenue'),
            DB::raw('COUNT(*) as bookings')
        )
            ->where('status', 'completed')
            ->whereBetween('booking_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function bookingsByStatus(?string $from = null, ?string $to = null)
    {
        [$start, $end] = $this->dateRange($from, $to);

        return Booking::select('status', DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('status')
            ->pluck('total', 'status');
    }

    public function paymentsByMethod(?string $from = null, ?string $to = null)
    {
        [$start, $end] = $this->dateRange($from, $to);

        return Booking::select(
            DB::raw("COALESCE(payment_method, 'unpaid') as method"),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(price) as amount')
        )
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'paid')
            ->groupBy('method')
            ->get();
    }

    public function topRoutes(?string $from = null, ?string $to = null, int $limit = 10)
    {
        [$start, $end] = $this->dateRange($from, $to);

        return Booking::with(['apartment', 'busStand'])
            ->select(
                'apartment_id',
                'bus_stand_id',
                DB::raw('COUNT(*) as trip_count'),
                DB::raw('SUM(price) as revenue')
            )
            ->whereBetween('created_at', [$start, $end])
            ->whereNotNull('apartment_id')
            ->whereNotNull('bus_stand_id')
            ->groupBy('apartment_id', 'bus_stand_id')
            ->orderByDesc('trip_count')
            ->limit($limit)
            ->get();
    }

    public function recentPayments(?string $from = null, ?string $to = null, int $limit = 15)
    {
        [$start, $end] = $this->dateRange($from, $to);

        return Booking::with(['customer', 'apartment', 'busStand'])
            ->whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'paid')
            ->latest('paid_at')
            ->limit($limit)
            ->get();
    }

    public function monthlyTrend(int $months = 12)
    {
        $start = now()->subMonths($months - 1)->startOfMonth();

        return Booking::select(
            DB::raw("DATE_FORMAT(booking_date, '%Y-%m') as month"),
            DB::raw('SUM(CASE WHEN status = "completed" THEN price ELSE 0 END) as revenue'),
            DB::raw('COUNT(*) as bookings')
        )
            ->where('booking_date', '>=', $start)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
