@extends('website.layout')

@section('title', 'Pre-Book Ride')

@section('content')
<div class="card-modern p-4">
    <h3 class="fw-bold mb-1">Pre-Book a Ride</h3>
    <p class="text-muted mb-4">Schedule rides for future dates</p>

    <form action="{{ route('customer.storePreBook') }}" method="POST">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Apartment</label>
                <select name="apartment_id" class="form-select" required>
                    @foreach($apartments as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Bus Stand</label>
                <select name="bus_stand_id" class="form-select" required>
                    @foreach($busStands as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Date</label>
                <input type="date" name="booking_date" class="form-control" min="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Time Slot</label>
                <select name="slot_time" class="form-select" required>
                    @foreach($slots as $slot)
                    <option value="{{ $slot->slot_time }}">{{ \Carbon\Carbon::parse($slot->slot_time)->format('h:i A') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Trip Type</label>
                <select name="trip_type" class="form-select">
                    <option value="apartment_to_busstand">Apartment → Bus Stand</option>
                    <option value="busstand_to_apartment">Bus Stand → Apartment</option>
                </select>
            </div>
        </div>
        @error('booking')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
        <button class="btn btn-brand mt-4">Create Pre-Booking</button>
    </form>
</div>
@endsection
