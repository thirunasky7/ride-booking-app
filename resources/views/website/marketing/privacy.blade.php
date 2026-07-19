@extends('layouts.marketing')
@section('title', 'Privacy Policy')
@section('content')
<div class="container py-5">
    <div class="page-header mb-4"><h1 class="fw-bold mb-0">Privacy Policy</h1></div>
    <div class="card-modern p-4">
        <p class="text-muted small">Last updated: {{ now()->toFormattedDateString() }}</p>
        <p>Apartment Shuttle (“we”, “our”, “us”) operates the Apartment Shuttle mobile applications and website. This Privacy Policy explains what information we collect and how we use it.</p>

        <h5 class="fw-bold mt-4">Information we collect</h5>
        <ul>
            <li>Mobile number used for OTP login</li>
            <li>Booking details (route, date, time, fare, payment status)</li>
            <li>Subscription information if you purchase a pass</li>
            <li>Device and basic app usage data needed to keep the service secure</li>
        </ul>

        <h5 class="fw-bold mt-4">How we use information</h5>
        <ul>
            <li>To authenticate you and manage your account</li>
            <li>To create and manage rides and subscriptions</li>
            <li>To communicate booking updates</li>
            <li>To improve safety, reliability, and customer support</li>
        </ul>

        <h5 class="fw-bold mt-4">Sharing</h5>
        <p>We share trip details with assigned drivers/operators only as needed to fulfill your ride. We do not sell your personal data.</p>

        <h5 class="fw-bold mt-4">Data retention & deletion</h5>
        <p>You may delete your account from the app (Profile → Delete account) or request deletion via <a href="{{ route('marketing.account-deletion') }}">Account deletion</a>. When deleted, your account and related personal booking records are removed from our active systems, subject to legal retention requirements.</p>

        <h5 class="fw-bold mt-4">Security</h5>
        <p>We use HTTPS and token-based authentication. Please keep your device secure and do not share OTP codes.</p>

        <h5 class="fw-bold mt-4">Contact</h5>
        <p class="mb-0">For privacy questions, contact us through the <a href="{{ route('marketing.contact') }}">Contact</a> page.</p>
    </div>
</div>
@endsection
