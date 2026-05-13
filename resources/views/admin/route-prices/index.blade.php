@extends('admin.layout')

@section('content')

<div class="d-flex justify-content-between mb-3">

    <h3>Route Pricing</h3>

    <a href="{{ route('route-prices.create') }}"
       class="btn btn-primary">

        Add Price

    </a>

</div>

<table class="table table-bordered">

    <thead>

        <tr>

            <th>Apartment</th>

            <th>Bus Stand</th>

            <th>Base Price</th>

            <th>Peak Price</th>

            <th>Holiday Price</th>

            <th>Action</th>

        </tr>

    </thead>

    <tbody>

        @foreach($prices as $price)

        <tr>

            <td>
                {{ $price->apartment?->name }}
            </td>

            <td>
                {{ $price->busStand?->name }}
            </td>

            <td>
                ₹{{ $price->base_price }}
            </td>

            <td>
                ₹{{ $price->peak_price }}
            </td>

            <td>
                ₹{{ $price->holiday_price }}
            </td>

            <td>

                <a href="{{ route('route-prices.edit',$price->id) }}"
                   class="btn btn-warning btn-sm">

                    Edit

                </a>

                <form action="{{ route('route-prices.destroy',$price->id) }}"
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

    </tbody>

</table>

@endsection