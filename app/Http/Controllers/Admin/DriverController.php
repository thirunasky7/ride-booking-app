<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::latest()->paginate(10);

        return view('admin.drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('admin.drivers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => 'required|unique:drivers',
            'password' => 'required|min:6',
        ]);

        Driver::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'license_number' => $request->license_number,
            'password' => bcrypt($request->password),
            'status' => $request->status,
        ]);

        return redirect()
            ->route('drivers.index')
            ->with('success', 'Driver Added');
    }

    public function edit(Driver $driver)
    {
        return view('admin.drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => 'required|unique:drivers,mobile,' . $driver->id,
        ]);

        $data = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'license_number' => $request->license_number,
            'status' => $request->status,
        ];

        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }

        $driver->update($data);

        return redirect()
            ->route('drivers.index')
            ->with('success', 'Driver Updated');
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();

        return back()->with('success', 'Driver Deleted');
    }
}