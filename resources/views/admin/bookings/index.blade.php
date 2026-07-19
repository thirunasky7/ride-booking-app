@extends('admin.layout')

@section('title', 'Bookings')

@section('content')

@include('admin.partials.page-header', [
    'title' => 'Bookings',
    'subtitle' => 'Manage ride bookings',
    'action' => route('bookings.create'),
    'actionLabel' => 'Add Booking',
])

<x-table :headers="['ID', 'Customer', 'Vehicle', 'Apartment', 'Bus Stand', 'Date', 'Slot', 'Trip Type', 'Status', 'Actions']">
    @forelse($bookings as $booking)
    <tr>
        <td><span class="badge bg-light text-dark">#{{ $booking->id }}</span></td>
        <td class="fw-semibold">{{ $booking->customer?->name }}</td>
        <td>{{ $booking->vehicle?->vehicle_number }}</td>
        <td>{{ $booking->apartment?->name }}</td>
        <td>{{ $booking->busStand?->name }}</td>
        <td>{{ $booking->booking_date->format('d-m-Y') }}</td>
        <td>{{ \Carbon\Carbon::parse($booking->slot_time)->format('h:i A') }}</td>
        <td>{{ $booking->trip_type }}</td>
        <td>
            @if($booking->status == 'confirmed')
                <span class="badge bg-success">Confirmed</span>
            @elseif($booking->status == 'completed')
                <span class="badge bg-primary">Completed</span>
            @else
                <span class="badge bg-danger">Cancelled</span>
            @endif
        </td>
        <td>
            <div class="d-flex gap-1">
                <x-button href="{{ route('bookings.edit', $booking) }}" variant="outline" size="sm" icon="pencil">Edit</x-button>
                <form action="{{ route('bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm" icon="trash">Delete</x-button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="10" class="text-center text-muted py-4">No bookings found.</td></tr>
    @endforelse
    <x-slot name="pagination">{{ $bookings->links() }}</x-slot>
</x-table>

@endsection
