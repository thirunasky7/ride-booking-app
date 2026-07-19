@extends('website.layout')

@section('title', 'My Bookings')

@section('content')

<div class="page-header mb-4">
    <h2 class="fw-bold mb-1">My Bookings</h2>
    <p class="mb-0 opacity-75 small">Track and manage your rides</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<x-table :headers="['Date', 'Time', 'Route', 'Type', 'Fare', 'Status', 'Action']">
    @forelse($bookings as $booking)
    <tr>
        <td>{{ $booking->booking_date->format('d M Y') }}</td>
        <td>{{ \Carbon\Carbon::parse($booking->slot_time)->format('h:i A') }}</td>
        <td class="small">
            @if($booking->pickup_address)
                <span class="text-muted">From:</span> {{ Str::limit($booking->pickup_address, 30) }}<br>
                <span class="text-muted">To:</span> {{ Str::limit($booking->drop_address, 30) }}
            @else
                {{ $booking->apartment?->name }} → {{ $booking->busStand?->name }}
            @endif
        </td>
        <td>
            <span class="badge bg-{{ $booking->booking_type === 'instant' ? 'info' : 'warning' }} text-dark">
                {{ ucfirst($booking->booking_type) }}
            </span>
        </td>
        <td class="fw-semibold text-success">₹{{ number_format($booking->price, 0) }}</td>
        <td>
            @php $colors = ['confirmed'=>'success','pending'=>'warning','started'=>'info','completed'=>'secondary','cancelled'=>'danger']; @endphp
            <span class="badge bg-{{ $colors[$booking->status] ?? 'secondary' }}">{{ ucfirst($booking->status) }}</span>
        </td>
        <td>
            @if(in_array($booking->status, ['confirmed', 'pending']))
            <form action="{{ route('customer.cancelBooking', $booking->id) }}" method="POST" onsubmit="return confirm('Cancel this booking?')">
                @csrf
                <x-button type="submit" variant="danger" size="sm" icon="x-circle">Cancel</x-button>
            </form>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="7" class="text-center text-muted py-5">
        <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>No bookings yet.
        <a href="{{ route('customer.bookRide') }}" class="btn btn-brand btn-sm mt-2">Book a Ride</a>
    </td></tr>
    @endforelse
    <x-slot name="pagination">{{ $bookings->links() }}</x-slot>
</x-table>

@endsection
