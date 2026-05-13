@extends('website.layout')

@section('content')

<div class="card border-0 shadow-sm">

    <div class="card-body">

        <h3 class="mb-4">
            My Bookings
        </h3>

        @if(session('success'))

        <div class="alert alert-success">

            {{ session('success') }}

        </div>

        @endif

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>Date</th>

                    <th>Time</th>

                    <th>Vehicle</th>

                    <th>Trip</th>

                    <th>Price</th>

                    <th>Status</th>

                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

                @foreach($bookings as $booking)

                <tr>

                    <td>

                        {{ $booking->booking_date->format('d-m-Y') }}

                    </td>

                    <td>

                        {{ \Carbon\Carbon::parse($booking->slot_time)->format('h:i A') }}

                    </td>

                    <td>

                        {{ $booking->vehicle?->vehicle_number }}

                    </td>

                    <td>

                        {{ $booking->trip_type }}

                    </td>

                    <td>

                        ₹{{ $booking->price }}

                    </td>

                    <td>

                        {{ ucfirst($booking->status) }}

                    </td>

                    <td>

                        @if(
                            $booking->status == 'confirmed'
                        )

                        <form action=
"{{ route('customer.cancelBooking',$booking->id) }}"
                              method="POST">

                            @csrf

                            <button class=
"btn btn-danger btn-sm">

                                Cancel

                            </button>

                        </form>

                        @endif

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

        {{ $bookings->links() }}

    </div>

</div>

@endsection