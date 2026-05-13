@extends('admin.layout')

@section('content')

<h3>Edit Booking</h3>

<form action="{{ route('bookings.update',$booking->id) }}"
      method="POST">

    @csrf
    @method('PUT')

    <div class="card">

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label>Status</label>

                    <select name="status"
                            class="form-control">

                        <option value="confirmed"
                            {{ $booking->status == 'confirmed' ? 'selected' : '' }}>

                            Confirmed

                        </option>

                        <option value="completed"
                            {{ $booking->status == 'completed' ? 'selected' : '' }}>

                            Completed

                        </option>

                        <option value="cancelled"
                            {{ $booking->status == 'cancelled' ? 'selected' : '' }}>

                            Cancelled

                        </option>

                    </select>

                </div>

            </div>

        </div>

    </div>

    <button class="btn btn-primary mt-3">

        Update Booking

    </button>

</form>

@endsection