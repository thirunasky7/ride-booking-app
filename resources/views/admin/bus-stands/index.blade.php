@extends('admin.layout')

@section('title', 'Bus Stands')

@section('content')

@include('admin.partials.page-header', [
    'title' => 'Bus Stands',
    'subtitle' => 'Manage drop-off locations',
    'action' => route('admin.bus-stands.create'),
    'actionLabel' => 'Add Bus Stand',
])

<x-table :headers="['ID', 'Name', 'Address', 'Status', 'Actions']">
    @forelse($busStands as $busStand)
    <tr>
        <td><span class="badge bg-light text-dark">#{{ $busStand->id }}</span></td>
        <td class="fw-semibold">{{ $busStand->name }}</td>
        <td class="text-muted small">{{ $busStand->address }}</td>
        <td>
            <span class="badge bg-{{ $busStand->status ? 'success' : 'secondary' }}">
                {{ $busStand->status ? 'Active' : 'Inactive' }}
            </span>
        </td>
        <td>
            <div class="d-flex gap-1">
                <x-button href="{{ route('admin.bus-stands.edit', $busStand) }}" variant="outline" size="sm" icon="pencil">Edit</x-button>
                <form action="{{ route('admin.bus-stands.destroy', $busStand) }}" method="POST" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm" icon="trash">Delete</x-button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center text-muted py-4">No bus stands found.</td></tr>
    @endforelse
    <x-slot name="pagination">{{ $busStands->links() }}</x-slot>
</x-table>

@endsection
