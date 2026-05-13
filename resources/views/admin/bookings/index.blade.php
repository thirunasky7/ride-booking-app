@extends('admin.layout')

@section('content')

<div class="d-flex justify-content-between mb-3">

    <h3>Bookings</h3>

    <a href="{{ route('bookings.create') }}"
       class="btn btn-primary">

        Add Booking

    </a>

</div>

@if(session('success'))

<div class="alert alert-success">

    {{ session('success') }}

</div>

@endif

<table class="table table-bordered table-striped">

    <thead>

        <tr>

            <th>ID</th>

            <th>Customer</th>

            <th>Vehicle</th>

            <th>Apartment</th>

            <th>Bus Stand</th>

            <th>Date</th>

            <th>Slot</th>

            <th>Trip Type</th>

            <th>Status</th>

            <th>Action</th>

        </tr>

    </thead>

    <tbody>

        @foreach($bookings as $booking)

        <tr>

            <td>{{ $booking->id }}</td>

            <td>{{ $booking->customer?->name }}</td>

            <td>{{ $booking->vehicle?->vehicle_number }}</td>

            <td>{{ $booking->apartment?->name }}</td>

            <td>{{ $booking->busStand?->name }}</td>

            <td>{{ $booking->booking_date->format('d-m-Y') }}</td>

            <td>
                {{ \Carbon\Carbon::parse($booking->slot_time)->format('h:i A') }}
            </td>

            <td>{{ $booking->trip_type }}</td>

            <td>

                @if($booking->status == 'confirmed')

                    <span class="badge bg-success">
                        Confirmed
                    </span>

                @elseif($booking->status == 'completed')

                    <span class="badge bg-primary">
                        Completed
                    </span>

                @else

                    <span class="badge bg-danger">
                        Cancelled
                    </span>

                @endif

            </td>

            <td>

                <a href="{{ route('bookings.edit',$booking->id) }}"
                   class="btn btn-warning btn-sm">

                    Edit

                </a>

                <form action="{{ route('bookings.destroy',$booking->id) }}"
                      method="POST"
                      class="d-inline">

                    @csrf
                    @method('DELETE')

                    <button class="btn btn-danger btn-sm">

                        Delete

                    </button>

                </form>

            </td>

        </tr>

        @endforeach

    </tbody>

</table>

{{ $bookings->links() }}

@endsection