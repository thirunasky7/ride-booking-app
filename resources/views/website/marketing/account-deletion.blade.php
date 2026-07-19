@extends('layouts.marketing')
@section('title', 'Account Deletion')
@section('content')
<div class="container py-5">
    <div class="page-header mb-4"><h1 class="fw-bold mb-0">Delete your account</h1></div>
    <div class="card-modern p-4">
        <p>Google Play requires a clear way for users to request account deletion. You can delete your Apartment Shuttle account in either of these ways:</p>
        <ol>
            <li><strong>In the app:</strong> open Profile → Delete account and confirm.</li>
            <li><strong>By request:</strong> contact us from the <a href="{{ route('marketing.contact') }}">Contact</a> page with your registered mobile number and subject “Account deletion”.</li>
        </ol>
        <h5 class="fw-bold mt-4">What is deleted</h5>
        <ul>
            <li>Your user account and login tokens</li>
            <li>OTP records for your mobile number</li>
            <li>Subscriptions linked to your account</li>
            <li>Your bookings from active systems</li>
        </ul>
        <p class="mb-0 text-muted">Deletion is permanent. Some records may be retained only where required by law (for example financial compliance).</p>
    </div>
</div>
@endsection
