@extends('website.layout')

@section('title', 'Dashboard')

@section('content')

<div class="welcome-banner card-modern p-4 p-lg-5 mb-4" style="background: linear-gradient(135deg, var(--brand-primary) 0%, #14b8a6 100%); border: none;">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <p class="text-white opacity-75 mb-1 small text-uppercase fw-semibold" style="letter-spacing: 0.08em;">Welcome back</p>
            <h2 class="text-white fw-bold mb-2">Hello, {{ auth()->user()->name }}!</h2>
            <p class="text-white opacity-75 mb-0 mb-lg-0">
                Manage your shuttle bookings and plan your next commute.
            </p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <a href="{{ route('customer.bookRide') }}" class="btn btn-light fw-semibold px-4" style="color: var(--brand-primary);">
                <i class="bi bi-plus-lg me-1"></i> New Booking
            </a>
        </div>
    </div>
</div>

@if(isset($activeSubscription) && $activeSubscription)
<div class="alert alert-success card-modern border-0 mb-4">
    <i class="bi bi-credit-card me-2"></i>
    <strong>{{ $activeSubscription->subscription->name }}</strong> plan active
    — {{ $activeSubscription->remaining_rides ?? '∞' }} rides remaining
    — expires {{ $activeSubscription->end_date->format('d M Y') }}
</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card-modern p-3 p-lg-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 48px; height: 48px; background: var(--brand-bg); color: var(--brand-primary); font-size: 1.25rem;">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div>
                    <div class="text-muted small">Upcoming</div>
                    <div class="fs-4 fw-bold" style="color: var(--brand-text);">{{ $upcomingCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card-modern p-3 p-lg-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 48px; height: 48px; background: #fef3c7; color: #d97706; font-size: 1.25rem;">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div>
                    <div class="text-muted small">Completed</div>
                    <div class="fs-4 fw-bold" style="color: var(--brand-text);">{{ $completedCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card-modern p-3 p-lg-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width: 48px; height: 48px; background: #ede9fe; color: #7c3aed; font-size: 1.25rem;">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <div class="text-muted small">Total Bookings</div>
                    <div class="fs-4 fw-bold" style="color: var(--brand-text);">{{ $totalCount }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <a href="{{ route('customer.bookRide') }}" class="text-decoration-none">
            <div class="card-modern p-4 h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 52px; height: 52px; background: linear-gradient(135deg, var(--brand-primary), #14b8a6); color: #fff; font-size: 1.4rem;">
                        <i class="bi bi-bus-front"></i>
                    </div>
                    <div>
                        <h5 class="fw-semibold mb-1" style="color: var(--brand-text);">Book a Ride</h5>
                        <p class="text-muted small mb-3">Schedule your next trip between apartment and bus stand.</p>
                        <span class="btn btn-brand btn-sm">Book Now <i class="bi bi-arrow-right ms-1"></i></span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="{{ route('customer.myBookings') }}" class="text-decoration-none">
            <div class="card-modern p-4 h-100">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 52px; height: 52px; background: #ecfdf5; color: #059669; font-size: 1.4rem; border: 2px solid #a7f3d0;">
                        <i class="bi bi-list-check"></i>
                    </div>
                    <div>
                        <h5 class="fw-semibold mb-1" style="color: var(--brand-text);">My Bookings</h5>
                        <p class="text-muted small mb-3">View history, track status, and cancel upcoming rides.</p>
                        <span class="btn btn-outline-brand btn-sm">View All <i class="bi bi-arrow-right ms-1"></i></span>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

@if($recentBooking)
<div class="card-modern p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-semibold mb-0" style="color: var(--brand-text);">
            <i class="bi bi-clock-history me-2"></i>Latest Booking
        </h5>
        <a href="{{ route('customer.myBookings') }}" class="small text-decoration-none fw-semibold" style="color: var(--brand-primary);">
            See all
        </a>
    </div>
    <div class="d-flex flex-wrap gap-4 align-items-center">
        <div>
            <div class="text-muted small">Date</div>
            <div class="fw-semibold">{{ $recentBooking->booking_date->format('d M Y') }}</div>
        </div>
        <div>
            <div class="text-muted small">Time</div>
            <div class="fw-semibold">{{ \Carbon\Carbon::parse($recentBooking->slot_time)->format('h:i A') }}</div>
        </div>
        <div>
            <div class="text-muted small">Route</div>
            <div class="fw-semibold">{{ $recentBooking->apartment?->name }} &rarr; {{ $recentBooking->busStand?->name }}</div>
        </div>
        <div>
            <div class="text-muted small">Status</div>
            @php
                $statusColors = [
                    'confirmed' => 'success',
                    'pending' => 'warning',
                    'started' => 'info',
                    'completed' => 'secondary',
                    'cancelled' => 'danger',
                ];
                $badge = $statusColors[$recentBooking->status] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $badge }}">{{ ucfirst($recentBooking->status) }}</span>
        </div>
    </div>
</div>
@endif

@endsection
