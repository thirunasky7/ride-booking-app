<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\DriverEarning;
use App\Services\DriverService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DriverApiController extends Controller
{
    use ApiResponse;

    public function __construct(protected DriverService $driverService) {}

    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'password' => 'required',
        ]);

        $driver = Driver::where('mobile', $request->mobile)->first();

        if (!$driver || !Hash::check($request->password, $driver->password)) {
            return $this->error('Invalid credentials.', 401);
        }

        $token = $driver->createToken('driver-token')->plainTextToken;

        return $this->success([
            'token' => $token,
            'driver' => $driver,
        ], 'Login successful.');
    }

    public function dashboard(Request $request)
    {
        $driver = $request->user();

        $todayTrips = Booking::whereHas('vehicle', fn ($q) => $q->where('driver_id', $driver->id))
            ->whereDate('booking_date', today())
            ->count();

        $completedTrips = Booking::whereHas('vehicle', fn ($q) => $q->where('driver_id', $driver->id))
            ->where('status', 'completed')
            ->count();

        $totalEarnings = DriverEarning::where('driver_id', $driver->id)->sum('driver_amount');

        return $this->success([
            'today_trips' => $todayTrips,
            'completed_trips' => $completedTrips,
            'total_earnings' => $totalEarnings,
        ]);
    }

    public function todayTrips(Request $request)
    {
        $driver = $request->user();

        $bookings = Booking::with(['customer', 'apartment', 'busStand'])
            ->whereHas('vehicle', fn ($q) => $q->where('driver_id', $driver->id))
            ->whereDate('booking_date', today())
            ->orderBy('slot_time')
            ->get();

        return $this->success(['trips' => $bookings]);
    }

    public function earnings(Request $request)
    {
        $earnings = DriverEarning::with('booking')
            ->where('driver_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return $this->success(['earnings' => $earnings]);
    }

    public function startTrip(Request $request, $id)
    {
        try {
            $this->driverService->startTrip($request->user(), (int) $id);

            return $this->success(null, 'Trip started.');
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    public function completeTrip(Request $request, $id)
    {
        try {
            $booking = $this->driverService->completeTrip($request->user(), (int) $id);

            return $this->success(['booking' => $booking], 'Trip completed.');
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage(), 403);
        }
    }

    public function toggleOnline(Request $request)
    {
        $request->validate(['is_online' => 'required|boolean']);

        $driver = $request->user();
        $driver->update(['is_online' => $request->boolean('is_online')]);

        return $this->success(['is_online' => $driver->is_online], 'Status updated.');
    }
}
