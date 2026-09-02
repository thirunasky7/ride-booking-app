@extends('admin.layout')

@section('title', 'Time Slots')

@section('content')

@include('admin.partials.page-header', [
    'title' => 'Time Slots',
    'subtitle' => 'Manage booking time slots',
    'action' => route('admin.time-slots.create'),
    'actionLabel' => 'Add Time Slot',
])

<x-table :headers="['ID', 'Slot Time', 'Status', 'Actions']">
    @forelse($timeSlots as $slot)
    <tr>
        <td><span class="badge bg-light text-dark">#{{ $slot->id }}</span></td>
        <td class="fw-semibold">{{ \Carbon\Carbon::parse($slot->slot_time)->format('h:i A') }}</td>
        <td>
            <span class="badge bg-{{ $slot->status ? 'success' : 'secondary' }}">
                {{ $slot->status ? 'Active' : 'Inactive' }}
            </span>
        </td>
        <td>
            <div class="d-flex gap-1">
                <x-button href="{{ route('admin.time-slots.edit', $slot) }}" variant="outline" size="sm" icon="pencil">Edit</x-button>
                <form action="{{ route('admin.time-slots.destroy', $slot) }}" method="POST" onsubmit="return confirm('Delete Time Slot?')">
                    @csrf @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm" icon="trash">Delete</x-button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="4" class="text-center text-muted py-4">No time slots found.</td></tr>
    @endforelse
    <x-slot name="pagination">{{ $timeSlots->links() }}</x-slot>
</x-table>

@endsection
