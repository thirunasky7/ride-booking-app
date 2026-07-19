@extends('admin.layout')

@section('title', 'Route Pricing')

@section('content')

@include('admin.partials.page-header', [
    'title' => 'Route Pricing',
    'subtitle' => 'Manage apartment to bus stand fares',
    'action' => route('route-prices.create'),
    'actionLabel' => 'Add Price',
])

<x-table :headers="['Apartment', 'Bus Stand', 'Base Price', 'Peak Price', 'Holiday Price', 'Actions']">
    @forelse($prices as $price)
    <tr>
        <td class="fw-semibold">{{ $price->apartment?->name }}</td>
        <td>{{ $price->busStand?->name }}</td>
        <td>₹{{ $price->base_price }}</td>
        <td>₹{{ $price->peak_price }}</td>
        <td>₹{{ $price->holiday_price }}</td>
        <td>
            <div class="d-flex gap-1">
                <x-button href="{{ route('route-prices.edit', $price) }}" variant="outline" size="sm" icon="pencil">Edit</x-button>
                <form action="{{ route('route-prices.destroy', $price) }}" method="POST" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <x-button type="submit" variant="danger" size="sm" icon="trash">Delete</x-button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center text-muted py-4">No route prices found.</td></tr>
    @endforelse
    <x-slot name="pagination">{{ $prices->links() }}</x-slot>
</x-table>

@endsection
