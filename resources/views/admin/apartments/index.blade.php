@extends('admin.layout')

@section('content')

<div class="d-flex justify-content-between mb-3">

    <h3>Apartments</h3>

    <a href="{{ route('apartments.create') }}"
       class="btn btn-primary">
        Add Apartment
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

    @foreach($apartments as $apartment)

    <tr>

        <td>{{ $apartment->id }}</td>

        <td>{{ $apartment->name }}</td>

        <td>{{ $apartment->address }}</td>

        <td>
            {{ $apartment->status ? 'Active' : 'Inactive' }}
        </td>

        <td>

            <a href="{{ route('apartments.edit',$apartment->id) }}"
               class="btn btn-warning btn-sm">
                Edit
            </a>

            <form action="{{ route('apartments.destroy',$apartment->id) }}"
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

{{ $apartments->links() }}

@endsection