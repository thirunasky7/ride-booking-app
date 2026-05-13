@extends('admin.layout')

@section('content')

<h3>Edit Apartment</h3>

<form action="{{ route('apartments.update',$apartment->id) }}"
      method="POST">

    @csrf
    @method('PUT')

    <div class="mb-3">

        <label>Name</label>

        <input type="text"
               name="name"
               value="{{ $apartment->name }}"
               class="form-control">

    </div>

    <div class="mb-3">

        <label>Address</label>

        <textarea name="address"
                  class="form-control">{{ $apartment->address }}</textarea>

    </div>

    <div class="mb-3">

        <label>Status</label>

        <select name="status"
                class="form-control">

            <option value="1"
                {{ $apartment->status == 1 ? 'selected' : '' }}>
                Active
            </option>

            <option value="0"
                {{ $apartment->status == 0 ? 'selected' : '' }}>
                Inactive
            </option>

        </select>

    </div>

    <button class="btn btn-primary">
        Update
    </button>

</form>

@endsection