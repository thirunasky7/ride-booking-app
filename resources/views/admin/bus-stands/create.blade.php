@extends('admin.layout')

@section('content')

<h3>Add Bus Stand</h3>

<form action="{{ route('bus-stands.store') }}"
      method="POST">

    @csrf

    <div class="mb-3">

        <label>Name</label>

        <input type="text"
               name="name"
               class="form-control">

    </div>

    <div class="mb-3">

        <label>Address</label>

        <textarea name="address"
                  class="form-control"></textarea>

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