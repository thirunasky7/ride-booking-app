<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use RuntimeException;

class PaymentService
{
    public function __construct(
        protected SettingsService $settingsService,
    ) {}

    public function createOrder(Booking $booking): array
    {
        if (!$this->settingsService->isRazorpayEnabled()) {
            throw new RuntimeException('Online payment is not enabled. Please contact support.');
        }

        $settings = $this->settingsService->get();
        $api = new Api($settings->razorpay_key_id, $settings->razorpay_key_secret);

        $amountPaise = (int) round((float) $booking->price * 100);

        if ($amountPaise < 100) {
            throw new RuntimeException('Booking amount is too low for online payment.');
        }

        $order = $api->order->create([
            'receipt' => 'booking_'.$booking->id,
            'amount' => $amountPaise,
            'currency' => 'INR',
            'notes' => [
                'booking_id' => (string) $booking->id,
                'user_id' => (string) $booking->user_id,
            ],
        ]);

        $booking->update([
            'razorpay_order_id' => $order['id'],
            'payment_status' => 'pending',
        ]);

        return [
            'order_id' => $order['id'],
            'amount' => $amountPaise,
            'currency' => 'INR',
            'key_id' => $settings->razorpay_key_id,
            'booking_id' => $booking->id,
            'customer_name' => $booking->customer?->name ?? 'Customer',
            'customer_mobile' => $booking->customer?->mobile ?? '',
        ];
    }

    public function verifyAndCapture(Booking $booking, User $user, array $payload): Booking
    {
        if ($booking->user_id !== $user->id) {
            throw new RuntimeException('Unauthorized booking access.');
        }

        if ($booking->payment_status === 'paid') {
            return $booking;
        }

        if (!$this->settingsService->isRazorpayEnabled()) {
            throw new RuntimeException('Online payment is not enabled.');
        }

        $orderId = $payload['razorpay_order_id'] ?? null;
        $paymentId = $payload['razorpay_payment_id'] ?? null;
        $signature = $payload['razorpay_signature'] ?? null;

        if (!$orderId || !$paymentId || !$signature) {
            throw new RuntimeException('Incomplete payment details received.');
        }

        if ($booking->razorpay_order_id && $booking->razorpay_order_id !== $orderId) {
            throw new RuntimeException('Payment order mismatch.');
        }

        $settings = $this->settingsService->get();
        $api = new Api($settings->razorpay_key_id, $settings->razorpay_key_secret);

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature,
            ]);
        } catch (\Exception $e) {
            Log::warning('Razorpay signature verification failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            throw new RuntimeException('Payment verification failed. Please try again.');
        }

        $booking->update([
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'payment_status' => 'paid',
            'payment_method' => 'razorpay',
            'paid_at' => now(),
            'status' => $booking->status === 'pending' ? 'confirmed' : $booking->status,
        ]);

        return $booking->fresh(['vehicle', 'apartment', 'busStand']);
    }

    public function markPaidWithoutGateway(Booking $booking): Booking
    {
        $booking->update([
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'paid_at' => now(),
            'status' => $booking->status === 'pending' ? 'confirmed' : $booking->status,
        ]);

        return $booking->fresh(['vehicle', 'apartment', 'busStand']);
    }
}
