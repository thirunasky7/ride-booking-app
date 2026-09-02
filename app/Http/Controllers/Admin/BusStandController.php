<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusStand;

class BusStandController extends Controller
{
    public function index()
    {
        $busStands = BusStand::latest()->paginate(10);

        return view('admin.bus-stands.index', compact('busStands'));
    }

    public function create()
    {
        return view('admin.bus-stands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        BusStand::create($request->all());

        return redirect()
            ->route('admin.bus-stands.index')
            ->with('success', 'Bus Stand Added');
    }

    public function edit(BusStand $bus_stand)
    {
        return view('admin.bus-stands.edit', compact('bus_stand'));
    }

    public function update(Request $request, BusStand $bus_stand)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $bus_stand->update($request->all());

        return redirect()
            ->route('admin.bus-stands.index')
            ->with('success', 'Bus Stand Updated');
    }

    public function destroy(BusStand $bus_stand)
    {
        $bus_stand->delete();

        return back()->with('success', 'Bus Stand Deleted');
    }
}