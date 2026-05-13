@extends('admin.layout')

@section('content')

<div class="d-flex justify-content-between mb-3">

    <h3>Vehicles</h3>

    <a href="{{ route('vehicles.create') }}"
       class="btn btn-primary">
        Add Vehicle
    </a>

</div>

@if(session('success'))

<div class="alert alert-success">
    {{ session('success') }}
</div>

@endif

<table class="table table-bordered table-striped">

    <thead>

        <tr>

            <th>ID</th>

            <th>Driver</th>

            <th>Vehicle Name</th>

            <th>Vehicle Number</th>

            <th>Capacity</th>

            <th>Status</th>

            <th width="180">Action</th>

        </tr>

    </thead>

    <tbody>

        @forelse($vehicles as $vehicle)

        <tr>

            <td>{{ $vehicle->id }}</td>

            <td>
                {{ $vehicle->driver?->name }}
            </td>

            <td>
                {{ $vehicle->vehicle_name }}
            </td>

            <td>
                {{ $vehicle->vehicle_number }}
            </td>

            <td>
                {{ $vehicle->capacity }}
            </td>

            <td>

                @if($vehicle->status)

                    <span class="badge bg-success">
                        Active
                    </span>

                @else

                    <span class="badge bg-danger">
                        Inactive
                    </span>

                @endif

            </td>

            <td>

                <a href="{{ route('vehicles.edit',$vehicle->id) }}"
                   class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="{{ route('vehicles.destroy',$vehicle->id) }}"
                      method="POST"
                      class="d-inline">

                    @csrf
                    @method('DELETE')

                    <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Delete Vehicle?')">
                        Delete
                    </button>

                </form>

            </td>

        </tr>

        @empty

        <tr>

            <td colspan="7" class="text-center">
                No Vehicles Found
            </td>

        </tr>

        @endforelse

    </tbody>

</table>

{{ $vehicles->links() }}

@endsection