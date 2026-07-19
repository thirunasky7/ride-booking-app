@extends('admin.layout')

@section('title', 'Edit Route Price')

@section('content')
<div class="page-header">
    <h2 class="mb-0 fw-bold">Edit Route Price</h2>
</div>

<form action="{{ route('route-prices.update', $route_price) }}" method="POST">
    @csrf @method('PUT')
    <div class="card-modern p-4">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Apartment</label>
                <select name="apartment_id" class="form-select" required>
                    @foreach($apartments as $apartment)
                        <option value="{{ $apartment->id }}" @selected($route_price->apartment_id == $apartment->id)>{{ $apartment->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Bus Stand</label>
                <select name="bus_stand_id" class="form-select" required>
                    @foreach($busStands as $busStand)
                        <option value="{{ $busStand->id }}" @selected($route_price->bus_stand_id == $busStand->id)>{{ $busStand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Base Price</label>
                <input type="number" step="0.01" name="base_price" class="form-control" value="{{ $route_price->base_price }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Peak Price</label>
                <input type="number" step="0.01" name="peak_price" class="form-control" value="{{ $route_price->peak_price }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Holiday Price</label>
                <input type="number" step="0.01" name="holiday_price" class="form-control" value="{{ $route_price->holiday_price }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Peak From</label>
                <input type="time" name="peak_from" class="form-control" value="{{ $route_price->peak_from }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Peak To</label>
                <input type="time" name="peak_to" class="form-control" value="{{ $route_price->peak_to }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1" @selected($route_price->status == 1)>Active</option>
                    <option value="0" @selected($route_price->status == 0)>Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <button class="btn btn-brand mt-3">Update Price</button>
    <a href="{{ route('route-prices.index') }}" class="btn btn-outline-secondary mt-3">Cancel</a>
</form>
@endsection
