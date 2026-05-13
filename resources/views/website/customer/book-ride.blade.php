@extends('website.layout')

@section('content')

<div class="card border-0 shadow-sm">

    <div class="card-body">

        <h3 class="mb-4">
            Book Ride
        </h3>

        <form action=
"{{ route('customer.storeBooking') }}"
              method="POST">

            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label>
                        Apartment
                    </label>

                    <select name="apartment_id"
                            class="form-control">

                        @foreach($apartments as $apartment)

                        <option value="{{ $apartment->id }}">

                            {{ $apartment->name }}

                        </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6 mb-3">

                    <label>
                        Bus Stand
                    </label>

                    <select name="bus_stand_id"
                            class="form-control">

                        @foreach($busStands as $busStand)

                        <option value="{{ $busStand->id }}">

                            {{ $busStand->name }}

                        </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6 mb-3">

                    <label>
                        Booking Date
                    </label>

                    <input type="date"
                           name="booking_date"
                           class="form-control">

                </div>

                <div class="col-md-6 mb-3">

                    <label>
                        Time Slot
                    </label>

                    <select name="slot_time"
                            class="form-control">

                        @foreach($slots as $slot)

                        <option value="{{ $slot->slot_time }}">

                            {{ \Carbon\Carbon::parse($slot->slot_time)->format('h:i A') }}

                        </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6 mb-3">

                    <label>
                        Trip Type
                    </label>

                    <select name="trip_type"
                            class="form-control">

                        <option value=
"apartment_to_busstand">

                            Apartment To Bus Stand

                        </option>

                        <option value=
"busstand_to_apartment">

                            Bus Stand To Apartment

                        </option>

                    </select>

                </div>

            </div>

            <button class="btn btn-primary">

                Confirm Booking

            </button>

        </form>

    </div>

</div>

@endsection