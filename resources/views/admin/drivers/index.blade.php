@extends('admin.layout')

@section('title', 'Drivers')

@section('content')

@include('admin.partials.page-header', [
    'title' => 'Drivers',
    'subtitle' => 'Manage driver profiles',
    'action' => route('admin.drivers.create'),
    'actionLabel' => 'Add Driver',
])

<x-table :headers="['ID', 'Name', 'Mobile', 'License', 'Status', 'Actions']">
    @forelse($drivers as $driver)
    <tr>
        <td><span class="badge bg-light text-dark">#{{ $driver->id }}</span></td>
        <td class="fw-semibold">{{ $driver->name }}</td>
        <td>{{ $driver->mobile }}</td>
        <td class="text-muted small">{{ $driver->license_number }}</td>
        <td>
            <span class="badge bg-{{ $driver->status ? 'success' : 'secondary' }}">
                {{ $driver->status ? 'Active' : 'Inactive' }}
            </span>
        </td>
        <td>
            <div class="d-flex gap-1">
                <x-button href="{{ route('admin.drivers.edit', $driver) }}" variant="outline" size="sm" icon="pencil">Edit</x-button>
                <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm" icon="trash">Delete</x-button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center text-muted py-4">No drivers found.</td></tr>
    @endforelse
    <x-slot name="pagination">{{ $drivers->links() }}</x-slot>
</x-table>

@endsection
