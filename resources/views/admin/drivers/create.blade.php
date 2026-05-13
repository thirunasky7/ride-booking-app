@extends('admin.layout')

@section('content')

<h3>Add Driver</h3>

<form action="{{ route('drivers.store') }}"
      method="POST">

    @csrf

    <div class="mb-3">
        <label>Name</label>
        <input type="text"
               name="name"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Mobile</label>
        <input type="text"
               name="mobile"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>License Number</label>
        <input type="text"
               name="license_number"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password"
               name="password"
               class="form-control">
    </div>

    <div class="mb-3">

        <label>Status</label>

        <select name="status"
                class="form-control">

            <option value="1">Active</option>

            <option value="0">Inactive</option>

        </select>

    </div>

    <button class="btn btn-success">
        Save
    </button>

</form>

@endsection