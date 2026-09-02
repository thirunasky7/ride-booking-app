<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apartment;

class ApartmentController extends Controller
{
    public function index()
    {
        $apartments = Apartment::latest()->paginate(10);

        return view('admin.apartments.index', compact('apartments'));
    }

    public function create()
    {
        return view('admin.apartments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        Apartment::create($request->all());

        return redirect()
            ->route('admin.apartments.index')
            ->with('success', 'Apartment Added');
    }

    public function edit(Apartment $apartment)
    {
        return view('admin.apartments.edit', compact('apartment'));
    }

    public function update(Request $request, Apartment $apartment)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $apartment->update($request->all());

        return redirect()
            ->route('admin.apartments.index')
            ->with('success', 'Apartment Updated');
    }

    public function destroy(Apartment $apartment)
    {
        $apartment->delete();

        return back()->with('success', 'Apartment Deleted');
    }
}