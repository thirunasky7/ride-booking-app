@extends('admin.layout')

@section('title', 'Subscriptions')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h2 class="mb-0 fw-bold">Subscription Plans</h2>
    <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-light"><i class="bi bi-plus-lg me-1"></i>New Plan</a>
</div>

<div class="mb-3">
    <a href="{{ route('admin.subscriptions.subscribers') }}" class="btn btn-outline-light btn-sm text-dark border"><i class="bi bi-people me-1"></i>View Subscribers</a>
</div>

<div class="row g-3">
    @foreach($subscriptions as $plan)
    <div class="col-md-4">
        <div class="stat-card p-4 bg-white h-100">
            <h5 class="fw-bold">{{ $plan->name }}</h5>
            <p class="text-muted small">{{ $plan->description }}</p>
            <h3 class="text-primary">₹{{ number_format($plan->price, 0) }}</h3>
            <p class="small mb-2">{{ $plan->ride_limit ? $plan->ride_limit.' rides' : 'Unlimited rides' }} / {{ $plan->validity_days }} days</p>
            <span class="badge bg-{{ $plan->status ? 'success' : 'secondary' }}">{{ $plan->status ? 'Active' : 'Inactive' }}</span>
            <span class="badge bg-info text-dark">{{ $plan->user_subscriptions_count }} subscribers</span>
            <div class="mt-3 d-flex gap-2">
                <a href="{{ route('admin.subscriptions.edit', $plan) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                <form action="{{ route('admin.subscriptions.destroy', $plan) }}" method="POST" onsubmit="return confirm('Delete plan?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
{{ $subscriptions->links() }}
@endsection
