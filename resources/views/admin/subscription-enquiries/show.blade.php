@extends('admin.layout')

@section('title', 'Enquiry Details')

@section('content')

<div class="mb-4">
    <a href="{{ route('subscription-enquiries.index') }}" class="text-decoration-none small">
        <i class="bi bi-arrow-left me-1"></i> Back to enquiries
    </a>
    <h3 class="fw-bold mt-2 mb-1">Subscription Enquiry</h3>
    <p class="text-muted mb-0">Submitted {{ $enquiry->created_at->format('d M Y, h:i A') }}</p>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="text-muted small">Name</div>
                        <div class="fw-semibold">{{ $enquiry->name }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Mobile</div>
                        <div class="fw-semibold">{{ $enquiry->mobile }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Email</div>
                        <div class="fw-semibold">{{ $enquiry->email ?? '—' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Interested Plan</div>
                        <div class="fw-semibold">{{ $enquiry->subscription?->name ?? 'Not specified' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-muted small">Preferred Start</div>
                        <div class="fw-semibold">{{ $enquiry->preferred_start_date?->format('d M Y') ?? 'Flexible' }}</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Message</div>
                        <div>{{ $enquiry->message ?: '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Update Status</div>
            <div class="card-body">
                <form action="{{ route('subscription-enquiries.update', $enquiry) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['pending', 'contacted', 'closed'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $enquiry->status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Notes</label>
                        <textarea name="admin_notes" class="form-control" rows="4">{{ old('admin_notes', $enquiry->admin_notes) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
