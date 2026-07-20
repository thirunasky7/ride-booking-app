@extends('layouts.marketing')
@section('title', 'Account Deletion')
@section('meta_description', 'How to delete your Apartment Shuttle (Azhai) customer account and what data is removed.')
@section('content')
<div class="container py-5">
    <div class="page-header mb-4"><h1 class="fw-bold mb-0">Delete your account</h1></div>
    <div class="card-modern p-4">
        <p class="text-muted small">Last updated: {{ now()->toFormattedDateString() }}</p>
        <p>Apartment Shuttle (Azhai) provides this page so Google Play users can clearly see how to delete a customer account and what happens to their data.</p>

        <h5 class="fw-bold mt-4">Customer accounts (Apartment Shuttle / Azhai customer app)</h5>
        <p>You can delete your customer account in either of these ways:</p>
        <ol>
            <li>
                <strong>In the app (recommended):</strong> open the customer app → <strong>Profile</strong> → <strong>Delete account</strong> → confirm.
                This calls our API (<code>DELETE /api/account</code>) and removes your account immediately while you are signed in.
            </li>
            <li>
                <strong>By request (web):</strong> if you cannot use the app, contact us from the
                <a href="{{ route('marketing.contact') }}">Contact</a> page (or email
                <a href="mailto:support@apartmentshuttle.com">support@apartmentshuttle.com</a>)
                with your registered mobile number and the subject <strong>Account deletion</strong>.
                We process verified requests within a reasonable time (typically within 30 days).
            </li>
        </ol>

        <h5 class="fw-bold mt-4">What is deleted</h5>
        <p>When a customer account is deleted, we remove from our active systems:</p>
        <ul>
            <li>Your user account profile and login / access tokens</li>
            <li>OTP records associated with your mobile number</li>
            <li>Subscriptions linked to your account</li>
            <li>Your bookings and related personal booking records from active systems</li>
        </ul>
        <p>Deletion of a customer account is permanent and cannot be undone.</p>

        <h5 class="fw-bold mt-4">Data that may be retained</h5>
        <p class="mb-0">Some information may be retained only where required by law or legitimate operational needs (for example financial, tax, fraud-prevention, or dispute records). Retained data is limited to what is necessary and is not used for marketing.</p>

        <h5 class="fw-bold mt-4">Driver accounts</h5>
        <p>
            Driver accounts are managed differently and are not deleted through the customer app’s
            <strong>Delete account</strong> flow. Drivers who want their account removed should contact
            operations at <a href="mailto:drivers@apartmentshuttle.com">drivers@apartmentshuttle.com</a>
            or use the <a href="{{ route('marketing.contact') }}">Contact</a> page with subject
            <strong>Driver account deletion</strong>, including their registered mobile number.
        </p>

        <h5 class="fw-bold mt-4">More information</h5>
        <p class="mb-0">
            See our <a href="{{ route('marketing.privacy') }}">Privacy Policy</a> for how we collect and use data,
            and our <a href="{{ route('marketing.terms') }}">Terms of Service</a> for general service terms.
        </p>
    </div>
</div>
@endsection
