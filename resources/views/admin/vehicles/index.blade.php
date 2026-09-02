@extends('admin.layout')

@section('title', 'Vehicles')

@section('content')

@include('admin.partials.page-header', [
    'title' => 'Vehicles',
    'subtitle' => 'Manage fleet vehicles',
    'action' => route('admin.vehicles.create'),
    'actionLabel' => 'Add Vehicle',
])

<x-table :headers="['ID', 'Driver', 'Vehicle Name', 'Vehicle Number', 'Capacity', 'Status', 'Actions']">
    @forelse($vehicles as $vehicle)
    <tr>
        <td><span class="badge bg-light text-dark">#{{ $vehicle->id }}</span></td>
        <td>{{ $vehicle->driver?->name }}</td>
        <td class="fw-semibold">{{ $vehicle->vehicle_name }}</td>
        <td>{{ $vehicle->vehicle_number }}</td>
        <td>{{ $vehicle->capacity }}</td>
        <td>
            <span class="badge bg-{{ $vehicle->status ? 'success' : 'secondary' }}">
                {{ $vehicle->status ? 'Active' : 'Inactive' }}
            </span>
        </td>
        <td>
            <div class="d-flex gap-1">
                <x-button href="{{ route('admin.vehicles.edit', $vehicle) }}" variant="outline" size="sm" icon="pencil">Edit</x-button>
                <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" onsubmit="return confirm('Delete Vehicle?')">
                    @csrf @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm" icon="trash">Delete</x-button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="7" class="text-center text-muted py-4">No vehicles found.</td></tr>
    @endforelse
    <x-slot name="pagination">{{ $vehicles->links() }}</x-slot>
</x-table>

@endsection
