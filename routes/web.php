<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ApartmentController;
use App\Http\Controllers\Admin\BusStandController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\TimeSlotController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\RoutePriceController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Website\CustomerAuthController;
use App\Http\Controllers\Website\CustomerBookingController;
use App\Http\Controllers\Website\MarketingController;

Route::get('/', [MarketingController::class, 'home'])->name('home');
Route::get('/about', [MarketingController::class, 'about'])->name('marketing.about');
Route::get('/services', [MarketingController::class, 'services'])->name('marketing.services');
Route::get('/pricing', [MarketingController::class, 'pricing'])->name('marketing.pricing');
Route::get('/contact', [MarketingController::class, 'contact'])->name('marketing.contact');
Route::get('/driver-register', [MarketingController::class, 'driverRegister'])->name('marketing.driver-register');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('apartments', ApartmentController::class);
    Route::resource('bus-stands', BusStandController::class);
    Route::resource('drivers', DriverController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('time-slots', TimeSlotController::class);
    Route::resource('bookings', BookingController::class);
    Route::get('booking-calendar', [BookingController::class, 'calendar'])->name('bookings.calendar');
    Route::resource('route-prices', RoutePriceController::class);
    Route::resource('subscriptions', SubscriptionController::class);
    Route::get('subscribers', [SubscriptionController::class, 'subscribers'])->name('subscriptions.subscribers');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('customer')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'login'])->name('customer.login');
    Route::post('/send-otp', [CustomerAuthController::class, 'sendOtp'])->name('customer.sendOtp');
    Route::post('/verify-otp', [CustomerAuthController::class, 'verifyOtp'])->name('customer.verifyOtp');

    Route::middleware('customer')->group(function () {
        Route::get('/calculate-price', [CustomerBookingController::class, 'calculatePrice'])->name('customer.calculate-price');
        Route::get('/available-slots', [CustomerBookingController::class, 'availableSlots'])->name('customer.available-slots');
        Route::get('/dashboard', [CustomerBookingController::class, 'dashboard'])->name('customer.dashboard');
        Route::get('/book-ride', [CustomerBookingController::class, 'create'])->name('customer.bookRide');
        Route::post('/store-booking', [CustomerBookingController::class, 'store'])->name('customer.storeBooking');
        Route::get('/pre-book', [CustomerBookingController::class, 'preBookForm'])->name('customer.preBook');
        Route::post('/store-pre-book', [CustomerBookingController::class, 'storePreBook'])->name('customer.storePreBook');
        Route::get('/scheduled-bookings', [CustomerBookingController::class, 'preBookings'])->name('customer.preBookings');
        Route::get('/subscriptions', [CustomerBookingController::class, 'subscriptions'])->name('customer.subscriptions');
        Route::post('/subscriptions/purchase', [CustomerBookingController::class, 'purchaseSubscription'])->name('customer.purchaseSubscription');
        Route::get('/my-bookings', [CustomerBookingController::class, 'myBookings'])->name('customer.myBookings');
        Route::post('/cancel-booking/{id}', [CustomerBookingController::class, 'cancelBooking'])->name('customer.cancelBooking');
    });
});

require __DIR__.'/auth.php';
