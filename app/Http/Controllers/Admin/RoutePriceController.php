<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoutePrice;
use App\Models\Apartment;
use App\Models\BusStand;

class RoutePriceController extends Controller
{
    public function index()
    {
        $prices = RoutePrice::with([
            'apartment',
            'busStand'
        ])->latest()->paginate(20);

        return view(
            'admin.route-prices.index',
            compact('prices')
        );
    }

    public function create()
    {
        $apartments = Apartment::all();

        $busStands = BusStand::all();

        return view(
            'admin.route-prices.create',
            compact(
                'apartments',
                'busStands'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([

            'apartment_id' => 'required',

            'bus_stand_id' => 'required',

            'base_price' => 'required',

        ]);

        RoutePrice::create($request->all());

        return redirect()
            ->route('route-prices.index')
            ->with(
                'success',
                'Price Added Successfully'
            );
    }

    public function edit(RoutePrice $route_price)
    {
        $apartments = Apartment::all();

        $busStands = BusStand::all();

        return view(
            'admin.route-prices.edit',
            compact(
                'route_price',
                'apartments',
                'busStands'
            )
        );
    }

    public function update(
        Request $request,
        RoutePrice $route_price
    ) {

        $route_price->update($request->all());

        return redirect()
            ->route('route-prices.index')
            ->with(
                'success',
                'Price Updated Successfully'
            );
    }

    public function destroy(RoutePrice $route_price)
    {
        $route_price->delete();

        return back()->with(
            'success',
            'Price Deleted'
        );
    }
}