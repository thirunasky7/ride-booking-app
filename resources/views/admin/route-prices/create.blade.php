@extends('admin.layout')

@section('content')

<h3>Add Route Price</h3>

<form action="{{ route('route-prices.store') }}"
      method="POST">

    @csrf

    <div class="card">

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label>Apartment</label>

                    <select name="apartment_id"
                            class="form-control">

                        @foreach($apartments as $apartment)

                        <option value="{{ $apartment->id }}">

                            {{ $apartment->name }}

                        </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-6 mb-3">

                    <label>Bus Stand</label>

                    <select name="bus_stand_id"
                            class="form-control">

                        @foreach($busStands as $busStand)

                        <option value="{{ $busStand->id }}">

                            {{ $busStand->name }}

                        </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-4 mb-3">

                    <label>Base Price</label>

                    <input type="number"
                           step="0.01"
                           name="base_price"
                           class="form-control">

                </div>

                <div class="col-md-4 mb-3">

                    <label>Peak Price</label>

                    <input type="number"
                           step="0.01"
                           name="peak_price"
                           class="form-control">

                </div>

                <div class="col-md-4 mb-3">

                    <label>Holiday Price</label>

                    <input type="number"
                           step="0.01"
                           name="holiday_price"
                           class="form-control">

                </div>

                <div class="col-md-6 mb-3">

                    <label>Peak From</label>

                    <input type="time"
                           name="peak_from"
                           class="form-control">

                </div>

                <div class="col-md-6 mb-3">

                    <label>Peak To</label>

                    <input type="time"
                           name="peak_to"
                           class="form-control">

                </div>

                <div class="col-md-6 mb-3">

                    <label>Status</label>

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

    </div>

    <button class="btn btn-success mt-3">

        Save Price

    </button>

</form>

@endsection