<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Driver;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('driver')
            ->latest()
            ->paginate(10);

        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $drivers = Driver::where('status', 1)->get();

        return view('admin.vehicles.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'driver_id' => 'required',
            'vehicle_name' => 'required',
            'vehicle_number' => 'required|unique:vehicles',
            'capacity' => 'required|integer|min:1',
        ]);

        Vehicle::create([
            'driver_id' => $request->driver_id,
            'vehicle_name' => $request->vehicle_name,
            'vehicle_number' => $request->vehicle_number,
            'capacity' => $request->capacity,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.vehicles.index')
            ->with('success', 'Vehicle Added Successfully');
    }

    public function edit(Vehicle $vehicle)
    {
        $drivers = Driver::where('status', 1)->get();

        return view(
            'admin.vehicles.edit',
            compact('vehicle', 'drivers')
        );
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'driver_id' => 'required',
            'vehicle_name' => 'required',
            'vehicle_number' => 'required|unique:vehicles,vehicle_number,' . $vehicle->id,
            'capacity' => 'required|integer|min:1',
        ]);

        $vehicle->update([
            'driver_id' => $request->driver_id,
            'vehicle_name' => $request->vehicle_name,
            'vehicle_number' => $request->vehicle_number,
            'capacity' => $request->capacity,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.vehicles.index')
            ->with('success', 'Vehicle Updated Successfully');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return back()->with('success', 'Vehicle Deleted Successfully');
    }
}