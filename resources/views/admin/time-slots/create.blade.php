@extends('admin.layout')

@section('content')

<h3>Add Time Slot</h3>

<form action="{{ route('admin.time-slots.store') }}"
      method="POST">

    @csrf

    <div class="card">

        <div class="card-body">

            <div class="mb-3">

                <label class="form-label">
                    Slot Time
                </label>

                <input type="time"
                       name="slot_time"
                       class="form-control"
                       value="{{ old('slot_time') }}">

                @error('slot_time')

                <small class="text-danger">
                    {{ $message }}
                </small>

                @enderror

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
        Save Time Slot
    </button>

</form>

@endsection