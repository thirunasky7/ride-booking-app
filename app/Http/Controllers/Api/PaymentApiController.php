<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\PaymentService;
use App\Services\SettingsService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use RuntimeException;

class PaymentApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected PaymentService $paymentService,
        protected BookingService $bookingService,
        protected SettingsService $settingsService,
    ) {}

    public function config()
    {
        $settings = $this->settingsService->get();

        return $this->success([
            'razorpay_enabled' => $this->settingsService->isRazorpayEnabled(),
            'razorpay_key_id' => $settings->razorpay_key_id,
            'site_name' => $settings->site_name,
        ]);
    }

    public function createOrder(Request $request, $id)
    {
        try {
            $booking = Booking::where('user_id', auth()->id())->findOrFail($id);
            $order = $this->paymentService->createOrder($booking);

            return $this->success([
                'booking_id' => $booking->id,
                'payment' => $order,
            ]);
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function verify(Request $request)
    {
        $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        try {
            $booking = Booking::where('user_id', auth()->id())->findOrFail($request->booking_id);
            $this->paymentService->verifyAndCapture($booking, auth()->user(), $request->only([
                'razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature',
            ]));
            $this->bookingService->finalizeAfterPayment($booking->fresh(), auth()->user());

            return $this->success([
                'booking' => $booking->fresh(['vehicle', 'apartment', 'busStand']),
            ], 'Payment successful.');
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
