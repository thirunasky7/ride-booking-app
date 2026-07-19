@extends('layouts.marketing')
@section('title', 'Pricing')
@section('content')
<div class="container py-5">
    <div class="page-header mb-4 text-center"><h1 class="fw-bold mb-2">Subscription Plans</h1><p class="mb-0 opacity-75">Choose a plan that fits your commute</p></div>
    <div class="row g-4 justify-content-center">
        @foreach($plans as $plan)
        <div class="col-md-4">
            <div class="card-modern p-4 h-100 text-center {{ $plan->name === 'Commuter' ? 'border-success border-2' : '' }}">
                @if($plan->name === 'Commuter')<span class="badge bg-success mb-2">Most Popular</span>@endif
                <h4>{{ $plan->name }}</h4>
                <h2 class="text-success">₹{{ number_format($plan->price, 0) }}<small class="fs-6 text-muted">/mo</small></h2>
                <p class="text-muted">{{ $plan->description }}</p>
                <ul class="list-unstyled small text-start mb-4">
                    <li><i class="bi bi-check text-success me-2"></i>{{ $plan->ride_limit ? $plan->ride_limit.' rides' : 'Unlimited rides' }}</li>
                    <li><i class="bi bi-check text-success me-2"></i>{{ $plan->validity_days }} days validity</li>
                    <li><i class="bi bi-check text-success me-2"></i>Pre-booking included</li>
                </ul>
                <a href="{{ route('customer.login') }}" class="btn btn-brand w-100">Get Started</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
