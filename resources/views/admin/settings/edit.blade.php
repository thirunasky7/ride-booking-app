@extends('admin.layout')

@section('title', 'Settings')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">App Settings</h3>
        <p class="text-muted mb-0">Configure booking rules, pricing, and payment gateway</p>
    </div>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">General</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings->site_name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Support Phone</label>
                        <input type="text" name="support_phone" class="form-control" value="{{ old('support_phone', $settings->support_phone) }}" placeholder="9876543210">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Support Email</label>
                        <input type="email" name="support_email" class="form-control" value="{{ old('support_email', $settings->support_email) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">Booking Rules</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Slot Gap (minutes)</label>
                        <input type="number" name="slot_gap_minutes" class="form-control" value="{{ old('slot_gap_minutes', $settings->slot_gap_minutes) }}" min="5" max="120" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Booking Opens</label>
                            <input type="time" name="booking_open_time" class="form-control" value="{{ old('booking_open_time', $settings->booking_open_time ? \Carbon\Carbon::parse($settings->booking_open_time)->format('H:i') : '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Booking Closes</label>
                            <input type="time" name="booking_close_time" class="form-control" value="{{ old('booking_close_time', $settings->booking_close_time ? \Carbon\Carbon::parse($settings->booking_close_time)->format('H:i') : '') }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Commission (%)</label>
                        <input type="number" step="0.01" name="commission_percent" class="form-control" value="{{ old('commission_percent', $settings->commission_percent) }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Custom Route Price (₹)</label>
                        <input type="number" step="0.01" name="custom_route_price" class="form-control" value="{{ old('custom_route_price', $settings->custom_route_price) }}" required>
                        <div class="form-text">Used when pickup/drop is a custom address.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-credit-card me-2"></i>Razorpay Payment Gateway</span>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="razorpay_enabled" value="1" id="razorpay_enabled"
                            {{ old('razorpay_enabled', $settings->razorpay_enabled) ? 'checked' : '' }}>
                        <label class="form-check-label" for="razorpay_enabled">Enable online payments</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info small mb-3">
                        Get your API keys from <a href="https://dashboard.razorpay.com/app/keys" target="_blank" rel="noopener">Razorpay Dashboard</a>.
                        Use test keys for development.
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Key ID</label>
                            <input type="text" name="razorpay_key_id" class="form-control" value="{{ old('razorpay_key_id', $settings->razorpay_key_id) }}" placeholder="rzp_test_...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Key Secret</label>
                            <input type="password" name="razorpay_key_secret" class="form-control" placeholder="{{ $settings->razorpay_key_secret ? '•••••••• (leave blank to keep current)' : 'Enter key secret' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i> Save Settings
        </button>
    </div>
</form>

@endsection
