@extends('admin.layout')

@section('content')

<h3>Edit Time Slot</h3>

<form action="{{ route('admin.time-slots.update',$time_slot->id) }}"
      method="POST">

    @csrf
    @method('PUT')

    <div class="card">

        <div class="card-body">

            <div class="mb-3">

                <label class="form-label">
                    Slot Time
                </label>

                <input type="time"
                       name="slot_time"
                       class="form-control"
                       value="{{ $time_slot->slot_time }}">

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

                    <option value="1"
                        {{ $time_slot->status == 1 ? 'selected' : '' }}>

                        Active

                    </option>

                    <option value="0"
                        {{ $time_slot->status == 0 ? 'selected' : '' }}>

                        Inactive

                    </option>

                </select>

            </div>

        </div>

    </div>

    <button class="btn btn-primary mt-3">
        Update Time Slot
    </button>

</form>

@endsection