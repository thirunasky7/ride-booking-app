<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\SubscriptionEnquiryRequest;
use App\Models\Apartment;
use App\Models\Booking;
use App\Models\BusStand;
use App\Models\Subscription;
use App\Models\SubscriptionEnquiry;
use App\Models\TimeSlot;
use App\Services\BookingService;
use App\Services\PaymentService;
use App\Services\SettingsService;
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
        protected PaymentService $paymentService,
        protected SettingsService $settingsService,
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
        $settings = $this->settingsService->get();

        return view('website.customer.book-ride', [
            'slots' => $slots,
            'apartments' => Apartment::where('status', 1)->orderBy('name')->get(),
            'busStands' => BusStand::where('status', 1)->orderBy('name')->get(),
            'razorpayEnabled' => $this->settingsService->isRazorpayEnabled(),
            'razorpayKeyId' => $settings->razorpay_key_id,
        ]);
    }

    public function store(BookingRequest $request)
    {
        try {
            $booking = $this->bookingService->create(auth()->user(), $request->validated());

            if ($this->settingsService->isRazorpayEnabled()) {
                $order = $this->paymentService->createOrder($booking);

                if ($request->expectsJson()) {
                    return $this->success([
                        'booking_id' => $booking->id,
                        'payment' => $order,
                    ]);
                }

                return view('website.customer.payment', [
                    'booking' => $booking,
                    'payment' => $order,
                ]);
            }

            $this->bookingService->finalizeAfterPayment(
                $this->paymentService->markPaidWithoutGateway($booking),
                auth()->user()
            );

            if ($request->expectsJson()) {
                return $this->success(['redirect' => route('customer.myBookings')], 'Booking created successfully.');
            }

            return redirect()->route('customer.myBookings')
                ->with('success', 'Booking created successfully.');
        } catch (RuntimeException $e) {
            if ($request->expectsJson()) {
                return $this->error($e->getMessage(), 422);
            }

            return back()->withErrors(['booking' => $e->getMessage()])->withInput();
        }
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        try {
            $booking = Booking::where('user_id', auth()->id())->findOrFail($request->booking_id);
            $this->paymentService->verifyAndCapture($booking, auth()->user(), $request->only([
                'razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature',
            ]));
            $this->bookingService->finalizeAfterPayment($booking->fresh(), auth()->user());

            if ($request->expectsJson()) {
                return $this->success(['redirect' => route('customer.myBookings')], 'Payment successful.');
            }

            return redirect()->route('customer.myBookings')
                ->with('success', 'Payment successful! Your booking is confirmed.');
        } catch (RuntimeException $e) {
            if ($request->expectsJson()) {
                return $this->error($e->getMessage(), 422);
            }

            return back()->withErrors(['payment' => $e->getMessage()]);
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

    public function submitSubscriptionEnquiry(SubscriptionEnquiryRequest $request)
    {
        SubscriptionEnquiry::create([
            'user_id' => auth()->id(),
            'subscription_id' => $request->subscription_id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'message' => $request->message,
            'preferred_start_date' => $request->preferred_start_date,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Thank you! Our team will contact you about your monthly subscription plan.');
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
