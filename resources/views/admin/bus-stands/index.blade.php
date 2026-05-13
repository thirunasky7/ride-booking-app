@extends('admin.layout')

@section('content')

<div class="d-flex justify-content-between mb-3">

    <h3>Bus Stands</h3>

    <a href="{{ route('bus-stands.create') }}"
       class="btn btn-primary">
        Add Bus Stand
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
        <th>Address</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    @foreach($busStands as $busStand)

    <tr>

        <td>{{ $busStand->id }}</td>

        <td>{{ $busStand->name }}</td>

        <td>{{ $busStand->address }}</td>

        <td>
            {{ $busStand->status ? 'Active' : 'Inactive' }}
        </td>

        <td>

            <a href="{{ route('bus-stands.edit',$busStand->id) }}"
               class="btn btn-warning btn-sm">
                Edit
            </a>

            <form action="{{ route('bus-stands.destroy',$busStand->id) }}"
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

{{ $busStands->links() }}

@endsection