@extends('website.layout')

@section('title', 'Home')

@section('content')

<section class="hero-section text-center py-5 py-lg-6 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <span class="badge rounded-pill px-3 py-2 mb-3" style="background: var(--brand-bg); color: var(--brand-primary); font-weight: 600;">
                <i class="bi bi-lightning-charge me-1"></i> Fast &amp; Reliable Shuttle Service
            </span>
            <h1 class="display-5 fw-bold mb-3" style="color: var(--brand-text); letter-spacing: -0.03em;">
                Your daily ride between<br class="d-none d-md-block"> apartment &amp; bus stand
            </h1>
            <p class="lead text-muted mb-4 mx-auto" style="max-width: 520px;">
                Book scheduled shuttle rides in seconds. Pick your time slot, choose your route, and travel comfortably every day.
            </p>
            <div class="d-flex flex-wrap gap-3 justify-content-center">
                <a href="{{ route('customer.login') }}" class="btn btn-brand btn-lg px-4">
                    <i class="bi bi-phone me-2"></i>Get Started
                </a>
                <a href="#how-it-works" class="btn btn-outline-brand btn-lg px-4">
                    How it works
                </a>
            </div>
        </div>
    </div>
</section>

<section class="mb-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-modern p-4 h-100 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 56px; height: 56px; background: var(--brand-bg); color: var(--brand-primary); font-size: 1.5rem;">
                    <i class="bi bi-calendar2-check"></i>
                </div>
                <h5 class="fw-semibold mb-2">Scheduled Rides</h5>
                <p class="text-muted small mb-0">Choose from available time slots that fit your daily commute schedule.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-modern p-4 h-100 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 56px; height: 56px; background: #fef3c7; color: #d97706; font-size: 1.5rem;">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <h5 class="fw-semibold mb-2">Door-to-Stand</h5>
                <p class="text-muted small mb-0">Travel between your apartment and the nearest bus stand with ease.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-modern p-4 h-100 text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width: 56px; height: 56px; background: #ede9fe; color: #7c3aed; font-size: 1.5rem;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h5 class="fw-semibold mb-2">Safe &amp; Tracked</h5>
                <p class="text-muted small mb-0">Assigned vehicles and confirmed bookings you can view anytime.</p>
            </div>
        </div>
    </div>
</section>

<section id="how-it-works" class="mb-5">
    <div class="card-modern p-4 p-lg-5">
        <h2 class="fw-bold text-center mb-4" style="color: var(--brand-text);">How it works</h2>
        <div class="row g-4">
            <div class="col-md-3 text-center">
                <div class="fw-bold rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                     style="width: 40px; height: 40px; background: var(--brand-primary); color: #fff;">1</div>
                <h6 class="fw-semibold">Login with OTP</h6>
                <p class="text-muted small mb-0">Enter your mobile number and verify with a one-time password.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="fw-bold rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                     style="width: 40px; height: 40px; background: var(--brand-primary); color: #fff;">2</div>
                <h6 class="fw-semibold">Pick your route</h6>
                <p class="text-muted small mb-0">Select apartment, bus stand, date, and preferred time slot.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="fw-bold rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                     style="width: 40px; height: 40px; background: var(--brand-primary); color: #fff;">3</div>
                <h6 class="fw-semibold">Confirm booking</h6>
                <p class="text-muted small mb-0">A vehicle is assigned automatically and your ride is confirmed.</p>
            </div>
            <div class="col-md-3 text-center">
                <div class="fw-bold rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                     style="width: 40px; height: 40px; background: var(--brand-primary); color: #fff;">4</div>
                <h6 class="fw-semibold">Ride &amp; track</h6>
                <p class="text-muted small mb-0">View upcoming rides and manage bookings from your dashboard.</p>
            </div>
        </div>
    </div>
</section>

<section class="text-center pb-3">
    <div class="card-modern p-5" style="background: linear-gradient(135deg, var(--brand-primary), #14b8a6); border: none;">
        <h3 class="text-white fw-bold mb-2">Ready to book your ride?</h3>
        <p class="text-white opacity-75 mb-4">Join residents who commute smarter every day.</p>
        <a href="{{ route('customer.login') }}" class="btn btn-light btn-lg fw-semibold px-4" style="color: var(--brand-primary);">
            <i class="bi bi-arrow-right-circle me-2"></i>Login &amp; Book Now
        </a>
    </div>
</section>

@endsection
