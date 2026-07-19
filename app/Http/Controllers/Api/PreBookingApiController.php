<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Services\BookingService;
use App\Traits\ApiResponse;
use RuntimeException;

class PreBookingApiController extends Controller
{
    use ApiResponse;

    public function __construct(protected BookingService $bookingService) {}

    public function index()
    {
        $bookings = Booking::with(['apartment', 'busStand', 'timeSlot'])
            ->where('user_id', auth()->id())
            ->where('booking_type', BookingService::TYPE_SCHEDULED)
            ->latest()
            ->get();

        return $this->success(['pre_bookings' => $bookings]);
    }

    public function store(BookingRequest $request)
    {
        try {
            $booking = $this->bookingService->create($request->user(), $request->validated());

            return $this->success(
                ['booking' => $booking],
                'Booking scheduled successfully.'
            );
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function confirm($id)
    {
        $booking = Booking::where('user_id', auth()->id())
            ->where('booking_type', BookingService::TYPE_SCHEDULED)
            ->where('status', 'pending')
            ->findOrFail($id);

        $booking->update(['status' => 'confirmed']);

        return $this->success(['booking' => $booking], 'Booking confirmed.');
    }

    public function cancel($id)
    {
        try {
            $booking = Booking::where('user_id', auth()->id())->findOrFail($id);
            $this->bookingService->cancel($booking, auth()->user());

            return $this->success(null, 'Booking cancelled.');
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
