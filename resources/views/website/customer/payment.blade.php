@extends('website.layout')

@section('title', 'Complete Payment')

@section('content')

<div class="row justify-content-center">
    <div class="col-md-6">
        <x-card title="Complete Your Payment" subtitle="Booking #{{ $booking->id }}">
            <div class="text-center mb-4">
                <div class="display-5 fw-bold text-success">₹{{ number_format($booking->price, 0) }}</div>
                <div class="text-muted small">Total fare for your ride</div>
            </div>

            <div class="mb-4 p-3 rounded-3" style="background:var(--brand-bg);">
                <div class="small text-muted">Route</div>
                <div class="fw-semibold">
                    {{ $booking->apartment?->name ?? $booking->pickup_address ?? 'Pickup' }}
                    →
                    {{ $booking->busStand?->name ?? $booking->drop_address ?? 'Drop' }}
                </div>
                <div class="small text-muted mt-2">
                    {{ $booking->booking_date->format('d M Y') }} ·
                    {{ \Carbon\Carbon::parse($booking->slot_time)->format('h:i A') }}
                </div>
            </div>

            <button type="button" id="payNowBtn" class="btn btn-brand w-100 py-3">
                <i class="bi bi-credit-card me-2"></i> Pay with Razorpay
            </button>

            <div id="paymentError" class="alert alert-danger mt-3 d-none"></div>
        </x-card>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    const payment = @json($payment);
    const bookingId = {{ $booking->id }};
    const verifyUrl = @json(route('customer.verifyPayment'));
    const csrf = @json(csrf_token());
    const redirectUrl = @json(route('customer.myBookings'));

    document.getElementById('payNowBtn').addEventListener('click', function () {
        const rzp = new Razorpay({
            key: payment.key_id,
            amount: payment.amount,
            currency: payment.currency,
            name: 'Apartment Shuttle',
            description: 'Ride booking payment',
            order_id: payment.order_id,
            prefill: { name: payment.customer_name, contact: payment.customer_mobile },
            theme: { color: '#0f766e' },
            handler: async function (response) {
                const res = await fetch(verifyUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({
                        booking_id: bookingId,
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_signature: response.razorpay_signature,
                    }),
                });
                const json = await res.json();
                if (json.status) window.location.href = redirectUrl;
                else {
                    document.getElementById('paymentError').textContent = json.message || 'Verification failed';
                    document.getElementById('paymentError').classList.remove('d-none');
                }
            },
        });
        rzp.open();
    });
</script>
@endpush
