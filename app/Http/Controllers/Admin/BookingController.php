<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Apartment;
use App\Models\Booking;
use App\Models\BusStand;
use App\Models\TimeSlot;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Http\Request;
use RuntimeException;

class BookingController extends Controller
{
    public function __construct(protected BookingService $bookingService) {}

    public function index()
    {
        $bookings = Booking::with(['customer', 'vehicle', 'apartment', 'busStand'])
            ->latest()
            ->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function calendar()
    {
        $bookings = Booking::with(['vehicle', 'customer', 'apartment', 'busStand'])
            ->orderBy('booking_date')
            ->get();

        return view('admin.bookings.calendar', compact('bookings'));
    }

    public function create()
    {
        $customers = User::where('role', 'customer')->get();
        $apartments = Apartment::where('status', 1)->get();
        $busStands = BusStand::where('status', 1)->get();
        $timeSlots = TimeSlot::where('status', 1)->get();

        return view('admin.bookings.create', compact('customers', 'apartments', 'busStands', 'timeSlots'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'apartment_id' => 'required|exists:apartments,id',
            'bus_stand_id' => 'required|exists:bus_stands,id',
            'booking_date' => 'required|date',
            'slot_time' => 'required',
            'time_slot_id' => 'nullable|exists:time_slots,id',
            'trip_type' => 'required|in:apartment_to_busstand,busstand_to_apartment',
        ]);

        try {
            $user = User::findOrFail($data['user_id']);
            $this->bookingService->create($user, $data);

            return redirect()->route('bookings.index')->with('success', 'Booking created successfully.');
        } catch (RuntimeException $e) {
            return back()->withErrors(['slot_time' => $e->getMessage()])->withInput();
        }
    }

    public function edit(Booking $booking)
    {
        $customers = User::where('role', 'customer')->get();
        $apartments = Apartment::where('status', 1)->get();
        $busStands = BusStand::where('status', 1)->get();
        $timeSlots = TimeSlot::where('status', 1)->get();

        return view('admin.bookings.edit', compact('booking', 'customers', 'apartments', 'busStands', 'timeSlots'));
    }

    public function update(Request $request, Booking $booking)
    {
        $request->validate(['status' => 'required']);

        $booking->update(['status' => $request->status]);

        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return back()->with('success', 'Booking deleted successfully.');
    }
}
