@extends('admin.layout')

@section('content')

<div class="d-flex justify-content-between mb-3">

    <h3>Drivers</h3>

    <a href="{{ route('drivers.create') }}"
       class="btn btn-primary">
        Add Driver
    </a>

</div>

@if(session('success'))

<div class="alert alert-success">
    {{ session('success') }}
</div>

@endif

<table class="table table-bordered">

    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Mobile</th>
        <th>License</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    @foreach($drivers as $driver)

    <tr>

        <td>{{ $driver->id }}</td>

        <td>{{ $driver->name }}</td>

        <td>{{ $driver->mobile }}</td>

        <td>{{ $driver->license_number }}</td>

        <td>
            {{ $driver->status ? 'Active' : 'Inactive' }}
        </td>

        <td>

            <a href="{{ route('drivers.edit',$driver->id) }}"
               class="btn btn-warning btn-sm">
                Edit
            </a>

            <form action="{{ route('drivers.destroy',$driver->id) }}"
                  method="POST"
                  class="d-inline">

                @csrf
                @method('DELETE')

                <button class="btn btn-danger btn-sm">
                    Delete
                </button>

            </form>

        </td>

    </tr>

    @endforeach

</table>

{{ $drivers->links() }}

@endsection