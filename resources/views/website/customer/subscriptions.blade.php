@extends('website.layout')

@section('title', 'Subscription Plans')

@section('content')

<div class="page-header mb-4">
    <h2 class="fw-bold mb-1">Subscription Plans</h2>
    <p class="mb-0 opacity-75 small">Save more with monthly ride bundles</p>
</div>

@if($activeSubscription)
<x-card class="mb-4" :padding="true">
    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-patch-check-fill text-success fs-3"></i>
        <div>
            <div class="fw-semibold">{{ $activeSubscription->subscription->name }} — Active</div>
            <div class="small text-muted">
                {{ $activeSubscription->remaining_rides ?? '∞' }} rides left ·
                Expires {{ $activeSubscription->end_date->format('d M Y') }}
            </div>
        </div>
    </div>
</x-card>
@endif

<div class="row g-4">
    @foreach($plans as $plan)
    <div class="col-md-4">
        <x-card class="h-100 text-center {{ $plan->name === 'Commuter' ? 'border border-success border-2' : '' }}">
            @if($plan->name === 'Commuter')<span class="badge bg-success mb-2">Popular</span>@endif
            <h5 class="fw-bold">{{ $plan->name }}</h5>
            <div class="display-6 fw-bold text-success my-2">₹{{ number_format($plan->price, 0) }}</div>
            <p class="text-muted small">{{ $plan->description }}</p>
            <ul class="list-unstyled small text-start mb-4">
                <li><i class="bi bi-check-circle text-success me-2"></i>{{ $plan->ride_limit ? $plan->ride_limit.' rides/month' : 'Unlimited rides' }}</li>
                <li><i class="bi bi-check-circle text-success me-2"></i>{{ $plan->validity_days }} days validity</li>
            </ul>
            <form action="{{ route('customer.purchaseSubscription') }}" method="POST">
                @csrf
                <input type="hidden" name="subscription_id" value="{{ $plan->id }}">
                <x-button type="submit" class="w-100" icon="cart-plus">Subscribe Now</x-button>
            </form>
        </x-card>
    </div>
    @endforeach
</div>

@endsection
