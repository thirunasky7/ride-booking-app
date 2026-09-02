<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeSlot;

class TimeSlotController extends Controller
{
    public function index()
    {
        $timeSlots = TimeSlot::latest()->paginate(20);

        return view('admin.time-slots.index', compact('timeSlots'));
    }

    public function create()
    {
        return view('admin.time-slots.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slot_time' => 'required',
        ]);

        $exists = TimeSlot::where(
            'slot_time',
            $request->slot_time
        )->exists();

        if ($exists) {

            return back()
                ->withErrors([
                    'slot_time' => 'Time Slot already exists'
                ])
                ->withInput();
        }

        TimeSlot::create([
            'slot_time' => $request->slot_time,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.time-slots.index')
            ->with('success', 'Time Slot Added Successfully');
    }

    public function edit(TimeSlot $time_slot)
    {
        return view(
            'admin.time-slots.edit',
            compact('time_slot')
        );
    }

    public function update(Request $request, TimeSlot $time_slot)
    {
        $request->validate([
            'slot_time' => 'required',
        ]);

        $exists = TimeSlot::where(
            'slot_time',
            $request->slot_time
        )
        ->where('id', '!=', $time_slot->id)
        ->exists();

        if ($exists) {

            return back()
                ->withErrors([
                    'slot_time' => 'Time Slot already exists'
                ])
                ->withInput();
        }

        $time_slot->update([
            'slot_time' => $request->slot_time,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('admin.time-slots.index')
            ->with('success', 'Time Slot Updated Successfully');
    }

    public function destroy(TimeSlot $time_slot)
    {
        $time_slot->delete();

        return back()
            ->with('success', 'Time Slot Deleted Successfully');
    }
}