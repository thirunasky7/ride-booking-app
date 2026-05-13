@extends('admin.layout')

@section('content')

<h3>Booking Calendar</h3>

<table class="table table-bordered">

    <thead>

        <tr>

            <th>Date</th>

            <th>Time</th>

            <th>Customer</th>

            <th>Vehicle</th>

            <th>Status</th>

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

                {{ $booking->customer?->name }}

            </td>

            <td>

                {{ $booking->vehicle?->vehicle_number }}

            </td>

            <td>

                {{ ucfirst($booking->status) }}

            </td>

        </tr>

        @endforeach

    </tbody>

</table>

@endsection