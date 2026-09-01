@extends('website.layout')

@section('title', 'Book a Ride')

@section('content')

<div class="page-header mb-4 text-center text-md-start">
    <h2 class="fw-bold mb-1"><i class="bi bi-geo-alt text-success me-2"></i>Book Your Ride</h2>
    <p class="mb-0 text-muted">Choose pickup & drop, pick a time, and pay securely online.</p>
</div>

<div class="d-flex justify-content-center gap-2 mb-4 booking-steps">
    <div class="step-item active" data-step="1"><span>1</span> Route</div>
    <div class="step-line"></div>
    <div class="step-item" data-step="2"><span>2</span> Confirm & Pay</div>
</div>

<form action="{{ route('customer.storeBooking') }}" method="POST" id="bookingForm">
    @csrf

    <div class="booking-step-panel" id="step1">
        <x-card title="Where are you going?" subtitle="Select pickup and drop locations from the list">
            <div class="location-card p-3 rounded-3 mb-3" style="background: var(--brand-bg);">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="pickup_location" class="form-label fw-semibold small text-uppercase text-muted">
                            <i class="bi bi-circle-fill text-success me-1" style="font-size:.5rem;"></i> Pickup Location
                        </label>
                        <select name="pickup_location" id="pickup_location" class="form-select form-select-lg" required>
                            <option value="">Select pickup point</option>
                            <optgroup label="Apartments">
                                @foreach($apartments as $apartment)
                                <option value="apartment:{{ $apartment->id }}" @selected(old('pickup_location') === 'apartment:'.$apartment->id)>
                                    {{ $apartment->name }}
                                </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Bus Stands">
                                @foreach($busStands as $busStand)
                                <option value="busstand:{{ $busStand->id }}" @selected(old('pickup_location') === 'busstand:'.$busStand->id)>
                                    {{ $busStand->name }}
                                </option>
                                @endforeach
                            </optgroup>
                            <option value="other" @selected(old('pickup_location') === 'other')>Other (custom address)</option>
                        </select>
                    </div>

                    <div class="col-md-2 text-center d-none d-md-block">
                        <button type="button" class="btn btn-light rounded-circle swap-btn" id="swapLocations" title="Swap locations">
                            <i class="bi bi-arrow-down-up"></i>
                        </button>
                    </div>

                    <div class="col-md-5">
                        <label for="drop_location" class="form-label fw-semibold small text-uppercase text-muted">
                            <i class="bi bi-circle-fill text-danger me-1" style="font-size:.5rem;"></i> Drop Location
                        </label>
                        <select name="drop_location" id="drop_location" class="form-select form-select-lg" required>
                            <option value="">Select drop point</option>
                            <optgroup label="Apartments">
                                @foreach($apartments as $apartment)
                                <option value="apartment:{{ $apartment->id }}" @selected(old('drop_location') === 'apartment:'.$apartment->id)>
                                    {{ $apartment->name }}
                                </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Bus Stands">
                                @foreach($busStands as $busStand)
                                <option value="busstand:{{ $busStand->id }}" @selected(old('drop_location') === 'busstand:'.$busStand->id)>
                                    {{ $busStand->name }}
                                </option>
                                @endforeach
                            </optgroup>
                            <option value="other" @selected(old('drop_location') === 'other')>Other (custom address)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row g-3 d-none" id="customAddressFields">
                <div class="col-md-6">
                    <label for="pickup_address" class="form-label">Pickup Address</label>
                    <textarea name="pickup_address" id="pickup_address" class="form-control" rows="2"
                              placeholder="Enter full pickup address">{{ old('pickup_address') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="drop_address" class="form-label">Drop Address</label>
                    <textarea name="drop_address" id="drop_address" class="form-control" rows="2"
                              placeholder="Enter full drop address">{{ old('drop_address') }}</textarea>
                </div>
            </div>

            <hr class="my-4">

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="booking_date" class="form-label fw-semibold">Date</label>
                    <input type="date" name="booking_date" id="booking_date" class="form-control form-control-lg"
                           value="{{ old('booking_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="time_slot_id" class="form-label fw-semibold">Time Slot</label>
                    <select name="time_slot_id" id="time_slot_id" class="form-select form-select-lg" required>
                        <option value="">Loading slots…</option>
                        @foreach($slots as $slot)
                        <option value="{{ $slot->id }}" data-slot-time="{{ $slot->slot_time }}"
                            @selected(old('time_slot_id') == $slot->id)>
                            {{ \Carbon\Carbon::parse($slot->slot_time)->format('h:i A') }}
                        </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="slot_time" id="slot_time" value="{{ old('slot_time') }}">
                    <div id="slotsLoading" class="d-none small text-muted mt-2">
                        <span class="spinner-border spinner-border-sm"></span> Checking availability…
                    </div>
                </div>
            </div>

            <div id="farePreview" class="fare-preview mt-4 d-none">
                <div class="d-flex align-items-center justify-content-between p-3 rounded-3 border" style="background:#fff;">
                    <div>
                        <div class="small text-muted">Estimated Fare</div>
                        <div class="fs-2 fw-bold text-success" id="fareAmount">₹—</div>
                        <div class="small text-muted" id="fareType"></div>
                    </div>
                    <div id="fareLoading" class="d-none"><span class="spinner-border text-success"></span></div>
                    <i class="bi bi-currency-rupee fs-1 text-success opacity-25" id="fareIcon"></i>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <x-button type="button" class="btn-next px-4" data-next="2" icon="arrow-right">Continue</x-button>
            </div>
        </x-card>
    </div>

    <div class="booking-step-panel d-none" id="step2">
        <x-card title="Review & Pay">
            <div class="trip-summary rounded-3 p-4 mb-4" style="background: var(--brand-bg);">
                <div class="d-flex gap-3">
                    <div class="trip-line flex-shrink-0 pt-1">
                        <div class="trip-dot bg-success"></div>
                        <div class="trip-connector"></div>
                        <div class="trip-dot bg-danger"></div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="mb-4">
                            <div class="small text-muted">Pickup</div>
                            <div class="fw-semibold fs-5" id="sumPickup">—</div>
                        </div>
                        <div>
                            <div class="small text-muted">Drop</div>
                            <div class="fw-semibold fs-5" id="sumDrop">—</div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-6"><div class="small text-muted">Date</div><div class="fw-semibold" id="sumDate">—</div></div>
                    <div class="col-6"><div class="small text-muted">Time</div><div class="fw-semibold" id="sumTime">—</div></div>
                    <div class="col-12"><div class="small text-muted">Total Fare</div><div class="fw-bold text-success fs-3" id="sumFare">—</div></div>
                </div>
            </div>

            @error('booking')<div class="alert alert-danger">{{ $message }}</div>@enderror
            @error('payment')<div class="alert alert-danger">{{ $message }}</div>@enderror

            @if($razorpayEnabled)
            <div class="alert alert-light border small mb-3">
                <i class="bi bi-shield-check text-success me-1"></i>
                Secure payment powered by <strong>Razorpay</strong>
            </div>
            @else
            <div class="alert alert-warning small mb-3">
                <i class="bi bi-info-circle me-1"></i>
                Online payment is not configured. Your booking will be confirmed and you can pay the driver directly.
            </div>
            @endif

            <div id="paymentError" class="alert alert-danger d-none"></div>

            <div class="d-flex justify-content-between gap-2">
                <x-button type="button" variant="outline" class="btn-prev" data-prev="1" icon="arrow-left">Back</x-button>
                <x-button type="button" id="payAndBookBtn" icon="{{ $razorpayEnabled ? 'credit-card' : 'check-lg' }}">
                    {{ $razorpayEnabled ? 'Pay & Confirm Booking' : 'Confirm Booking' }}
                </x-button>
            </div>
        </x-card>
    </div>
</form>
@endsection

@push('styles')
<style>
.booking-steps .step-item { display:flex; align-items:center; gap:8px; font-size:.85rem; font-weight:600; color:var(--brand-muted); }
.booking-steps .step-item span { width:28px; height:28px; border-radius:50%; background:#e2e8f0; display:inline-flex; align-items:center; justify-content:center; font-size:.75rem; }
.booking-steps .step-item.active { color:var(--brand-primary); }
.booking-steps .step-item.active span { background:var(--brand-primary); color:#fff; }
.booking-steps .step-line { width:40px; height:2px; background:#e2e8f0; align-self:center; }
.swap-btn { width:44px; height:44px; border:2px solid var(--brand-border) !important; }
.trip-dot { width:12px; height:12px; border-radius:50%; }
.trip-connector { width:2px; height:48px; background:#cbd5e1; margin:4px auto; }
.fare-preview { animation: fadeIn .3s ease; }
@keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }
</style>
@endpush

@push('scripts')
<script>
    window.bookingConfig = {
        calculatePriceUrl: @json(route('customer.calculate-price')),
        availableSlotsUrl: @json(route('customer.available-slots')),
        storeBookingUrl: @json(route('customer.storeBooking')),
        verifyPaymentUrl: @json(route('customer.verifyPayment')),
        csrfToken: @json(csrf_token()),
        razorpayEnabled: @json($razorpayEnabled),
        razorpayKeyId: @json($razorpayKeyId),
        myBookingsUrl: @json(route('customer.myBookings')),
    };
</script>
@if($razorpayEnabled)
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endif
@endpush
