@extends('layouts.marketing')
@section('title', 'Terms of Service')
@section('content')
<div class="container py-5">
    <div class="page-header mb-4"><h1 class="fw-bold mb-0">Terms of Service</h1></div>
    <div class="card-modern p-4">
        <p class="text-muted small">Last updated: {{ now()->toFormattedDateString() }}</p>
        <p>By using Apartment Shuttle apps or website, you agree to these Terms.</p>

        <h5 class="fw-bold mt-4">Service</h5>
        <p>Apartment Shuttle provides apartment-to-bus-stand shuttle booking. Availability depends on vehicles, slots, and location coverage.</p>

        <h5 class="fw-bold mt-4">Accounts</h5>
        <p>You must provide an accurate mobile number. You are responsible for activity under your account. Do not share OTPs.</p>

        <h5 class="fw-bold mt-4">Bookings & payments</h5>
        <p>Fares are shown before confirmation. Payment status may be marked in-app (for example Cash/UPI). Cancel only when the booking status allows cancellation.</p>

        <h5 class="fw-bold mt-4">Acceptable use</h5>
        <ul>
            <li>No fraudulent bookings or misuse of the platform</li>
            <li>No harassment of drivers or staff</li>
            <li>No reverse engineering or unauthorized API access</li>
        </ul>

        <h5 class="fw-bold mt-4">Liability</h5>
        <p>We strive for reliable service but are not liable for delays caused by traffic, weather, or circumstances beyond our control.</p>

        <h5 class="fw-bold mt-4">Contact</h5>
        <p class="mb-0">Questions? Visit our <a href="{{ route('marketing.contact') }}">Contact</a> page.</p>
    </div>
</div>
@endsection
