@extends('website.layout')

@section('title', 'Subscription Plans')

@section('content')

<div class="page-header mb-4">
    <h2 class="fw-bold mb-1">Monthly Subscription Plans</h2>
    <p class="mb-0 text-muted">Save on daily commutes with a monthly ride bundle</p>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

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

<div class="row g-4 mb-5">
    @foreach($plans as $plan)
    <div class="col-md-4">
        <x-card class="h-100 text-center {{ $plan->name === 'Commuter' ? 'border border-success border-2' : '' }}">
            @if($plan->name === 'Commuter')<span class="badge bg-success mb-2">Popular</span>@endif
            <h5 class="fw-bold">{{ $plan->name }}</h5>
            <div class="display-6 fw-bold text-success my-2">₹{{ number_format($plan->price, 0) }}</div>
            <p class="text-muted small">{{ $plan->description }}</p>
            <ul class="list-unstyled small text-start mb-0">
                <li><i class="bi bi-check-circle text-success me-2"></i>{{ $plan->ride_limit ? $plan->ride_limit.' rides/month' : 'Unlimited rides' }}</li>
                <li><i class="bi bi-check-circle text-success me-2"></i>{{ $plan->validity_days }} days validity</li>
            </ul>
        </x-card>
    </div>
    @endforeach
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <x-card title="Request a Monthly Plan" subtitle="Fill in your details and our team will contact you with the best offer">
            <form action="{{ route('customer.subscriptionEnquiry') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mobile <span class="text-danger">*</span></label>
                        <input type="text" name="mobile" class="form-control" value="{{ old('mobile', auth()->user()->mobile) }}" maxlength="10" required>
                        @error('mobile')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Interested Plan</label>
                        <select name="subscription_id" class="form-select">
                            <option value="">Any plan / Not sure</option>
                            @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" @selected(old('subscription_id') == $plan->id)>
                                {{ $plan->name }} — ₹{{ number_format($plan->price, 0) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Preferred Start Date</label>
                        <input type="date" name="preferred_start_date" class="form-control"
                               value="{{ old('preferred_start_date') }}" min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="3"
                                  placeholder="Tell us about your daily route, number of rides per week, etc.">{{ old('message') }}</textarea>
                    </div>
                    <div class="col-12">
                        <x-button type="submit" class="w-100" icon="send">Submit Enquiry</x-button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</div>

@endsection
