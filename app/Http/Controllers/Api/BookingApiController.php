<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\SubscriptionEnquiryRequest;
use App\Models\Booking;
use App\Models\Subscription;
use App\Models\SubscriptionEnquiry;
use App\Services\BookingService;
use App\Services\PaymentService;
use App\Services\SettingsService;
use App\Services\SubscriptionService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use RuntimeException;

class BookingApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected BookingService $bookingService,
        protected PaymentService $paymentService,
        protected SettingsService $settingsService,
    ) {}

    public function createBooking(BookingRequest $request)
    {
        try {
            $booking = $this->bookingService->create(
                $request->user(),
                $request->validated()
            );

            $data = ['booking' => $booking];

            if ($this->settingsService->isRazorpayEnabled()) {
                $data['payment'] = $this->paymentService->createOrder($booking);
            } else {
                $this->bookingService->finalizeAfterPayment(
                    $this->paymentService->markPaidWithoutGateway($booking),
                    $request->user()
                );
                $data['booking'] = $booking->fresh(['vehicle', 'apartment', 'busStand']);
            }

            return $this->success($data, 'Booking created successfully.');
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function bookingHistory()
    {
        $bookings = Booking::with(['vehicle', 'apartment', 'busStand'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return $this->success(['bookings' => $bookings]);
    }

    public function upcomingBookings()
    {
        $bookings = Booking::with(['vehicle', 'apartment', 'busStand'])
            ->where('user_id', auth()->id())
            ->whereIn('status', BookingService::ACTIVE_STATUSES)
            ->latest()
            ->get();

        return $this->success(['bookings' => $bookings]);
    }

    public function completedBookings()
    {
        $bookings = Booking::with(['vehicle', 'apartment', 'busStand'])
            ->where('user_id', auth()->id())
            ->where('status', 'completed')
            ->latest()
            ->get();

        return $this->success(['bookings' => $bookings]);
    }

    public function cancelBooking($id)
    {
        try {
            $booking = Booking::where('user_id', auth()->id())->findOrFail($id);
            $this->bookingService->cancel($booking, auth()->user());

            return $this->success(null, 'Booking cancelled.');
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,paid',
            'payment_method' => 'required_if:payment_status,paid|nullable|in:cash,upi',
        ]);

        try {
            $booking = Booking::where('user_id', auth()->id())->findOrFail($id);
            $updated = $this->bookingService->updatePaymentStatus(
                $booking,
                auth()->user(),
                $request->payment_status,
                $request->payment_method
            );

            return $this->success(['booking' => $updated], 'Payment status updated.');
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function modifyBooking(BookingRequest $request, $id)
    {
        try {
            $booking = Booking::where('user_id', auth()->id())->findOrFail($id);
            $updated = $this->bookingService->modify($booking, auth()->user(), $request->validated());

            return $this->success(['booking' => $updated], 'Booking updated successfully.');
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
