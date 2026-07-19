@extends('admin.layout')

@section('title', 'Create Plan')

@section('content')
<div class="page-header"><h2 class="mb-0 fw-bold">Create Subscription Plan</h2></div>
<form action="{{ route('subscriptions.store') }}" method="POST">
    @csrf
    <div class="card-modern p-4">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="col-md-6"><label class="form-label">Price (₹)</label><input type="number" step="0.01" name="price" class="form-control" required></div>
            <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
            <div class="col-md-4"><label class="form-label">Ride Limit (empty = unlimited)</label><input type="number" name="ride_limit" class="form-control"></div>
            <div class="col-md-4"><label class="form-label">Validity (days)</label><input type="number" name="validity_days" class="form-control" value="30" required></div>
            <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><option value="1">Active</option><option value="0">Inactive</option></select></div>
        </div>
    </div>
    <button class="btn btn-brand mt-3">Save Plan</button>
</form>
@endsection
