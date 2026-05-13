@extends('admin.layout')

@section('content')

<h3>Edit Bus Stand</h3>

<form action="{{ route('bus-stands.update',$bus_stand->id) }}"
      method="POST">

    @csrf
    @method('PUT')

    <div class="mb-3">

        <label>Name</label>

        <input type="text"
               name="name"
               value="{{ $bus_stand->name }}"
               class="form-control">

    </div>

    <div class="mb-3">

        <label>Address</label>

        <textarea name="address"
                  class="form-control">{{ $bus_stand->address }}</textarea>

    </div>

    <div class="mb-3">

        <label>Status</label>

        <select name="status"
                class="form-control">

            <option value="1"
                {{ $bus_stand->status == 1 ? 'selected' : '' }}>
                Active
            </option>

            <option value="0"
                {{ $bus_stand->status == 0 ? 'selected' : '' }}>
                Inactive
            </option>

        </select>

    </div>

    <button class="btn btn-primary">
        Update
    </button>

</form>

@endsection