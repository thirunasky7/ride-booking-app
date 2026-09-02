@extends('admin.layout')

@section('content')

<h3>Add Vehicle</h3>

<form action="{{ route('admin.vehicles.store') }}"
      method="POST">

    @csrf

    <div class="card">

        <div class="card-body">

            <div class="mb-3">

                <label class="form-label">
                    Driver
                </label>

                <select name="driver_id"
                        class="form-control">

                    <option value="">
                        Select Driver
                    </option>

                    @foreach($drivers as $driver)

                    <option value="{{ $driver->id }}">

                        {{ $driver->name }}
                        ({{ $driver->mobile }})

                    </option>

                    @endforeach

                </select>

                @error('driver_id')

                <small class="text-danger">
                    {{ $message }}
                </small>

                @enderror

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Vehicle Name
                </label>

                <input type="text"
                       name="vehicle_name"
                       class="form-control"
                       value="{{ old('vehicle_name') }}">

                @error('vehicle_name')

                <small class="text-danger">
                    {{ $message }}
                </small>

                @enderror

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Vehicle Number
                </label>

                <input type="text"
                       name="vehicle_number"
                       class="form-control"
                       value="{{ old('vehicle_number') }}">

                @error('vehicle_number')

                <small class="text-danger">
                    {{ $message }}
                </small>

                @enderror

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Capacity
                </label>

                <input type="number"
                       name="capacity"
                       class="form-control"
                       value="4">

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Status
                </label>

                <select name="status"
                        class="form-control">

                    <option value="1">
                        Active
                    </option>

                    <option value="0">
                        Inactive
                    </option>

                </select>

            </div>

        </div>

    </div>

    <button class="btn btn-success mt-3">
        Save Vehicle
    </button>

</form>

@endsection