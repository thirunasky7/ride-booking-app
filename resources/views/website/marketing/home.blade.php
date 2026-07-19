@extends('layouts.marketing')

@section('title', 'Home — Apartment Shuttle')
@section('meta_description', 'Book daily shuttle rides between your apartment and bus stand. Subscriptions, pre-booking, and real-time tracking.')

@section('content')
<section class="marketing-hero text-center">
    <div class="container py-5">
        <h1 class="display-4 fw-bold mb-3">Your Daily Commute, Simplified</h1>
        <p class="lead opacity-75 mx-auto mb-4" style="max-width:600px">Premium shuttle service connecting apartments to bus stands. Book daily rides or subscribe for unlimited savings.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                @auth
                    @if(auth()->user()->role === 'customer')
                    <a href="{{ route('customer.bookRide') }}" class="btn btn-light btn-lg fw-semibold text-success">Book a Ride</a>
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-light btn-lg">Dashboard</a>
                    @else
                    <a href="{{ route('customer.login') }}" class="btn btn-light btn-lg fw-semibold text-success">Get Started</a>
                    @endif
                @else
                <a href="{{ route('customer.login') }}" class="btn btn-light btn-lg fw-semibold text-success">Get Started</a>
                <a href="{{ route('marketing.pricing') }}" class="btn btn-outline-light btn-lg">View Plans</a>
                @endauth
            </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4"><div class="card-modern p-4"><i class="bi bi-calendar2-check fs-2 text-success"></i><h5 class="mt-3">Daily Booking</h5><p class="text-muted small">Book same-day rides anytime before your slot.</p></div></div>
            <div class="col-md-4"><div class="card-modern p-4"><i class="bi bi-calendar-plus fs-2 text-warning"></i><h5 class="mt-3">Pre-Booking</h5><p class="text-muted small">Schedule rides days or weeks ahead.</p></div></div>
            <div class="col-md-4"><div class="card-modern p-4"><i class="bi bi-credit-card fs-2 text-primary"></i><h5 class="mt-3">Subscriptions</h5><p class="text-muted small">Monthly plans with ride bundles or unlimited access.</p></div></div>
        </div>
    </div>
</section>

@if($plans->count())
<section class="py-5 bg-light">
    <div class="container text-center">
        <h2 class="fw-bold mb-4">Popular Plans</h2>
        <div class="row g-4 justify-content-center">
            @foreach($plans as $plan)
            <div class="col-md-4">
                <div class="card-modern p-4 h-100">
                    <h5>{{ $plan->name }}</h5>
                    <h2 class="text-success">₹{{ number_format($plan->price, 0) }}</h2>
                    <p class="text-muted small">{{ $plan->ride_limit ? $plan->ride_limit.' rides/mo' : 'Unlimited' }}</p>
                    <a href="{{ route('customer.login') }}" class="btn btn-brand">Subscribe</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-4">What Residents Say</h2>
        <div class="row g-4">
            <div class="col-md-4"><div class="card-modern p-4"><p class="fst-italic">"Reliable every morning. Booking takes 30 seconds."</p><strong>— Priya, Green Valley</strong></div></div>
            <div class="col-md-4"><div class="card-modern p-4"><p class="fst-italic">"The subscription plan saves me ₹500 every month."</p><strong>— Rahul, Sunrise Residency</strong></div></div>
            <div class="col-md-4"><div class="card-modern p-4"><p class="fst-italic">"Pre-booking for the whole week is a game changer."</p><strong>— Ananya, Lake View</strong></div></div>
        </div>
    </div>
</section>
@endsection
