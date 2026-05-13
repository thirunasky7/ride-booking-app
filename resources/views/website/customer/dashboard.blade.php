@extends('website.layout')

@section('content')

<div class="row">

    <div class="col-md-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center">

                <h4>
                    Book Ride
                </h4>

                <a href=
"{{ route('customer.bookRide') }}"
                   class="btn btn-primary mt-2">

                    Book Now

                </a>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center">

                <h4>
                    My Bookings
                </h4>

                <a href=
"{{ route('customer.myBookings') }}"
                   class="btn btn-success mt-2">

                    View Bookings

                </a>

            </div>

        </div>

    </div>

</div>

@endsection