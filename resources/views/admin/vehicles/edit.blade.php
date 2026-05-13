@extends('admin.layout')

@section('content')

<h3>Edit Vehicle</h3>

<form action="{{ route('vehicles.update',$vehicle->id) }}"
      method="POST">

    @csrf
    @method('PUT')

    <div class="card">

        <div class="card-body">

            <div class="mb-3">

                <label class="form-label">
                    Driver
                </label>

                <select name="driver_id"
                        class="form-control">

                    @foreach($drivers as $driver)

                    <option value="{{ $driver->id }}"
                        {{ $vehicle->driver_id == $driver->id ? 'selected' : '' }}>

                        {{ $driver->name }}
                        ({{ $driver->mobile }})

                    </option>

                    @endforeach

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Vehicle Name
                </label>

                <input type="text"
                       name="vehicle_name"
                       class="form-control"
                       value="{{ $vehicle->vehicle_name }}">

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Vehicle Number
                </label>

                <input type="text"
                       name="vehicle_number"
                       class="form-control"
                       value="{{ $vehicle->vehicle_number }}">

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Capacity
                </label>

                <input type="number"
                       name="capacity"
                       class="form-control"
                       value="{{ $vehicle->capacity }}">

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Status
                </label>

                <select name="status"
                        class="form-control">

                    <option value="1"
                        {{ $vehicle->status == 1 ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0"
                        {{ $vehicle->status == 0 ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

            </div>

        </div>

    </div>

    <button class="btn btn-primary mt-3">
        Update Vehicle
    </button>

</form>

@endsection