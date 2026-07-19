@extends('admin.layout')

@section('title', 'Apartments')

@section('content')

@include('admin.partials.page-header', [
    'title' => 'Apartments',
    'subtitle' => 'Manage pickup locations',
    'action' => route('apartments.create'),
    'actionLabel' => 'Add Apartment',
])

<x-table :headers="['ID', 'Name', 'Address', 'Status', 'Actions']">
    @forelse($apartments as $apartment)
    <tr>
        <td><span class="badge bg-light text-dark">#{{ $apartment->id }}</span></td>
        <td class="fw-semibold">{{ $apartment->name }}</td>
        <td class="text-muted small">{{ $apartment->address }}</td>
        <td>
            <span class="badge bg-{{ $apartment->status ? 'success' : 'secondary' }}">
                {{ $apartment->status ? 'Active' : 'Inactive' }}
            </span>
        </td>
        <td>
            <div class="d-flex gap-1">
                <x-button href="{{ route('apartments.edit', $apartment) }}" variant="outline" size="sm" icon="pencil">Edit</x-button>
                <form action="{{ route('apartments.destroy', $apartment) }}" method="POST" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm" icon="trash">Delete</x-button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center text-muted py-4">No apartments found.</td></tr>
    @endforelse
    <x-slot name="pagination">{{ $apartments->links() }}</x-slot>
</x-table>

@endsection
