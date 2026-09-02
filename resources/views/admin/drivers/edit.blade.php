@extends('admin.layout')

@section('content')

<h3>Edit Driver</h3>

<form action="{{ route('admin.drivers.update',$driver->id) }}"
      method="POST">

    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Name</label>
        <input type="text"
               name="name"
               value="{{ $driver->name }}"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Mobile</label>
        <input type="text"
               name="mobile"
               value="{{ $driver->mobile }}"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>License Number</label>
        <input type="text"
               name="license_number"
               value="{{ $driver->license_number }}"
               class="form-control">
    </div>

    <div class="mb-3">
        <label>Password (Optional)</label>
        <input type="password"
               name="password"
               class="form-control">
    </div>

    <div class="mb-3">

        <label>Status</label>

        <select name="status"
                class="form-control">

            <option value="1"
                {{ $driver->status == 1 ? 'selected' : '' }}>
                Active
            </option>

            <option value="0"
                {{ $driver->status == 0 ? 'selected' : '' }}>
                Inactive
            </option>

        </select>

    </div>

    <button class="btn btn-primary">
        Update
    </button>

</form>

@endsection