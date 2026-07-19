@extends('website.layout')

@section('title', 'Scheduled Bookings')

@section('content')

<div class="page-header mb-4">
    <h2 class="fw-bold mb-1">Scheduled Bookings</h2>
    <p class="mb-0 opacity-75 small">Future rides you've planned ahead</p>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<x-table :headers="['Date', 'Time', 'Route', 'Fare', 'Status']">
    @forelse($bookings as $booking)
    <tr>
        <td>{{ $booking->booking_date->format('d M Y') }}</td>
        <td>{{ \Carbon\Carbon::parse($booking->slot_time)->format('h:i A') }}</td>
        <td class="small">
            {{ $booking->pickup_address ? Str::limit($booking->pickup_address, 25) : $booking->apartment?->name }}
            →
            {{ $booking->drop_address ? Str::limit($booking->drop_address, 25) : $booking->busStand?->name }}
        </td>
        <td class="fw-semibold text-success">₹{{ number_format($booking->price, 0) }}</td>
        <td><span class="badge bg-{{ $booking->status === 'pending' ? 'warning' : 'success' }}">{{ ucfirst($booking->status) }}</span></td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center text-muted py-4">No scheduled bookings. <a href="{{ route('customer.bookRide') }}">Book for a future date</a></td></tr>
    @endforelse
    <x-slot name="pagination">{{ $bookings->links() }}</x-slot>
</x-table>

@endsection
