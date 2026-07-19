<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Apartment;
use App\Models\Booking;
use App\Models\BusStand;
use App\Models\Subscription;
use App\Models\TimeSlot;
use App\Services\BookingService;
use App\Services\PricingService;
use App\Services\SubscriptionService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use RuntimeException;

class CustomerBookingController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected BookingService $bookingService,
        protected SubscriptionService $subscriptionService,
        protected PricingService $pricingService,
    ) {}

    public function dashboard()
    {
        $userId = auth()->id();

        $upcomingCount = Booking::where('user_id', $userId)
            ->whereIn('status', BookingService::ACTIVE_STATUSES)
            ->where('booking_date', '>=', now()->toDateString())
            ->count();

        $completedCount = Booking::where('user_id', $userId)->where('status', 'completed')->count();
        $totalCount = Booking::where('user_id', $userId)->count();

        $recentBooking = Booking::with(['apartment', 'busStand'])
            ->where('user_id', $userId)->latest()->first();

        $activeSubscription = $this->subscriptionService->getActiveSubscription(auth()->user());

        return view('website.customer.dashboard', compact(
            'upcomingCount', 'completedCount', 'totalCount', 'recentBooking', 'activeSubscription'
        ));
    }

    public function create()
    {
        $slots = TimeSlot::where('status', 1)->get();

        return view('website.customer.book-ride', [
            'slots' => $slots,
            'apartments' => Apartment::where('status', 1)->orderBy('name')->get(),
            'busStands' => BusStand::where('status', 1)->orderBy('name')->get(),
        ]);
    }

    public function store(BookingRequest $request)
    {
        try {
            $this->bookingService->create(auth()->user(), $request->validated());

            return redirect()->route('customer.myBookings')
                ->with('success', 'Booking created successfully.');
        } catch (RuntimeException $e) {
            return back()->withErrors(['booking' => $e->getMessage()])->withInput();
        }
    }

    /** @deprecated Use create() — same unified booking flow */
    public function preBookForm()
    {
        return redirect()->route('customer.bookRide');
    }

    /** @deprecated Use store() */
    public function storePreBook(BookingRequest $request)
    {
        return $this->store($request);
    }

    /** Scheduled bookings from unified table */
    public function preBookings()
    {
        $bookings = Booking::with(['apartment', 'busStand'])
            ->where('user_id', auth()->id())
            ->where('booking_type', BookingService::TYPE_SCHEDULED)
            ->latest()
            ->paginate(20);

        return view('website.customer.pre-bookings', compact('bookings'));
    }

    public function calculatePrice(Request $request)
    {
        return app(\App\Http\Controllers\Api\PricingApiController::class)->calculatePrice($request);
    }

    public function availableSlots(Request $request)
    {
        return app(\App\Http\Controllers\Api\SlotApiController::class)->availableSlots($request);
    }

    public function subscriptions()
    {
        $plans = Subscription::where('status', true)->orderBy('price')->get();
        $activeSubscription = $this->subscriptionService->getActiveSubscription(auth()->user());

        return view('website.customer.subscriptions', compact('plans', 'activeSubscription'));
    }

    public function purchaseSubscription(Request $request)
    {
        $request->validate(['subscription_id' => 'required|exists:subscriptions,id']);

        try {
            $plan = Subscription::findOrFail($request->subscription_id);
            $this->subscriptionService->purchase(auth()->user(), $plan);

            return back()->with('success', 'Subscription activated successfully.');
        } catch (RuntimeException $e) {
            return back()->withErrors(['subscription' => $e->getMessage()]);
        }
    }

    public function myBookings()
    {
        $bookings = Booking::with(['vehicle', 'apartment', 'busStand'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('website.customer.my-bookings', compact('bookings'));
    }

    public function cancelBooking($id)
    {
        try {
            $booking = Booking::where('user_id', auth()->id())->findOrFail($id);
            $this->bookingService->cancel($booking, auth()->user());

            return back()->with('success', 'Booking cancelled.');
        } catch (RuntimeException $e) {
            return back()->withErrors(['booking' => $e->getMessage()]);
        }
    }
}
