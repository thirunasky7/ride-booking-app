@extends('website.layout')

@section('title', 'Book a Ride')

@section('content')

<div class="page-header mb-4">
    <h2 class="fw-bold mb-1">Book Your Ride</h2>
    <p class="mb-0 opacity-75 small">Instant booking for today · Schedule for future dates</p>
</div>

{{-- Step indicator --}}
<div class="d-flex justify-content-center gap-2 mb-4 booking-steps">
    <div class="step-item active" data-step="1"><span>1</span> Schedule</div>
    <div class="step-line"></div>
    <div class="step-item" data-step="2"><span>2</span> Confirm</div>
</div>

<form action="{{ route('customer.storeBooking') }}" method="POST" id="bookingForm">
    @csrf

    {{-- Step 1: Route & schedule --}}
    <div class="booking-step-panel" id="step1">
        <x-card title="Route & Schedule" subtitle="Select your route and preferred time">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <select name="trip_type" id="trip_type" class="form-select" required>
                            <option value="apartment_to_busstand" @selected(old('trip_type', 'apartment_to_busstand') === 'apartment_to_busstand')>Apartment → Bus Stand</option>
                            <option value="busstand_to_apartment" @selected(old('trip_type') === 'busstand_to_apartment')>Bus Stand → Apartment</option>
                            <option value="others" @selected(old('trip_type') === 'others')>Others (custom addresses)</option>
                        </select>
                        <label for="trip_type">Trip Type</label>
                    </div>
                </div>

                <div class="col-12" id="standardRouteFields">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select name="apartment_id" id="apartment_id" class="form-select route-required">
                                    <option value="">Select apartment</option>
                                    @foreach($apartments as $apartment)
                                    <option value="{{ $apartment->id }}" @selected(old('apartment_id') == $apartment->id)>
                                        {{ $apartment->name }}
                                    </option>
                                    @endforeach
                                    <option value="other" @selected(old('apartment_id') === 'other')>Other</option>
                                </select>
                                <label for="apartment_id">Apartment <span class="text-danger">*</span></label>
                            </div>
                            <div class="form-floating mb-3 d-none" id="otherApartmentWrap">
                                <input type="text" name="pickup_address" id="pickup_address_standard" class="form-control"
                                       value="{{ old('pickup_address') }}" placeholder="Enter pickup address">
                                <label for="pickup_address_standard">Pickup Address <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select name="bus_stand_id" id="bus_stand_id" class="form-select route-required">
                                    <option value="">Select bus stand</option>
                                    @foreach($busStands as $busStand)
                                    <option value="{{ $busStand->id }}" @selected(old('bus_stand_id') == $busStand->id)>
                                        {{ $busStand->name }}
                                    </option>
                                    @endforeach
                                    <option value="other" @selected(old('bus_stand_id') === 'other')>Other</option>
                                </select>
                                <label for="bus_stand_id">Bus Stand <span class="text-danger">*</span></label>
                            </div>
                            <div class="form-floating mb-3 d-none" id="otherBusStandWrap">
                                <input type="text" name="drop_address" id="drop_address_standard" class="form-control"
                                       value="{{ old('drop_address') }}" placeholder="Enter drop address">
                                <label for="drop_address_standard">Drop Address <span class="text-danger">*</span></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 d-none" id="customAddressFields">
                    <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <textarea name="pickup_address" id="pickup_address_custom" class="form-control custom-required"
                                      style="height:100px" placeholder="Enter pickup address">{{ old('pickup_address') }}</textarea>
                            <label for="pickup_address_custom">Pickup Address <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <textarea name="drop_address" id="drop_address_custom" class="form-control custom-required"
                                      style="height:100px" placeholder="Enter drop address">{{ old('drop_address') }}</textarea>
                            <label for="drop_address_custom">Drop Address <span class="text-danger">*</span></label>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="date" name="booking_date" id="booking_date" class="form-control"
                               value="{{ old('booking_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                        <label for="booking_date">Booking Date <span class="text-danger">*</span></label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <select name="time_slot_id" id="time_slot_id" class="form-select" required>
                            <option value="">Loading slots…</option>
                            @foreach($slots as $slot)
                            <option value="{{ $slot->id }}" data-slot-time="{{ $slot->slot_time }}"
                                @selected(old('time_slot_id') == $slot->id)>
                                {{ \Carbon\Carbon::parse($slot->slot_time)->format('h:i A') }}
                            </option>
                            @endforeach
                        </select>
                        <label for="time_slot_id">Time Slot <span class="text-danger">*</span></label>
                        <input type="hidden" name="slot_time" id="slot_time" value="{{ old('slot_time') }}">
                    </div>
                    <div id="slotsLoading" class="d-none text-center py-2">
                        <div class="spinner-border spinner-border-sm text-success"></div>
                        <span class="small text-muted ms-2">Loading availability…</span>
                    </div>
                </div>
            </div>

            {{-- Price preview --}}
            <div id="farePreview" class="fare-preview d-none">
                <div class="d-flex align-items-center justify-content-between p-3 rounded-3" style="background:var(--brand-bg);">
                    <div>
                        <div class="small text-muted">Estimated Fare</div>
                        <div class="fs-3 fw-bold text-success" id="fareAmount">₹—</div>
                        <div class="small text-muted" id="fareType"></div>
                    </div>
                    <div id="fareLoading" class="d-none">
                        <div class="spinner-border text-success"></div>
                    </div>
                    <i class="bi bi-currency-rupee fs-1 text-success opacity-25" id="fareIcon"></i>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <x-button type="button" class="btn-next" data-next="2" icon="arrow-right">Next: Confirm</x-button>
            </div>
        </x-card>
    </div>

    {{-- Step 2: Confirm --}}
    <div class="booking-step-panel d-none" id="step2">
        <x-card title="Confirm Booking">
            <div class="row g-3 mb-4" id="confirmSummary">
                <div class="col-sm-6"><div class="small text-muted" id="sumPickupLabel">Pickup</div><div class="fw-semibold" id="sumPickup">—</div></div>
                <div class="col-sm-6"><div class="small text-muted" id="sumDropLabel">Drop</div><div class="fw-semibold" id="sumDrop">—</div></div>
                <div class="col-sm-6"><div class="small text-muted">Trip</div><div class="fw-semibold" id="sumTrip">—</div></div>
                <div class="col-sm-6"><div class="small text-muted">Date</div><div class="fw-semibold" id="sumDate">—</div></div>
                <div class="col-sm-6"><div class="small text-muted">Time</div><div class="fw-semibold" id="sumTime">—</div></div>
                <div class="col-sm-6"><div class="small text-muted">Fare</div><div class="fw-bold text-success fs-5" id="sumFare">—</div></div>
            </div>
            @error('booking')<div class="alert alert-danger">{{ $message }}</div>@enderror
            @foreach(['pickup_address', 'drop_address', 'apartment_id', 'bus_stand_id', 'trip_type'] as $field)
                @error($field)<div class="alert alert-danger">{{ $message }}</div>@enderror
            @endforeach
            <div class="d-flex justify-content-between">
                <x-button type="button" variant="outline" class="btn-prev" data-prev="1" icon="arrow-left">Back</x-button>
                <x-button type="submit" icon="check-lg" id="confirmBtn">Confirm Booking</x-button>
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
.fare-preview { animation: fadeIn .3s ease; }
@keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }
</style>
@endpush

@push('scripts')
<script>
    window.bookingConfig = {
        calculatePriceUrl: @json(url('/api/calculate-price')),
        availableSlotsUrl: @json(url('/api/available-slots')),
        csrfToken: @json(csrf_token()),
    };
</script>
@endpush
