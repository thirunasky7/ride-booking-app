<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\DriverApiController;
use App\Http\Controllers\Api\SlotApiController;
use App\Http\Controllers\Api\PreBookingApiController;
use App\Http\Controllers\Api\SubscriptionApiController;
use App\Http\Controllers\Api\PricingApiController;
use App\Http\Controllers\Api\CatalogApiController;

Route::post('/driver/login', [DriverApiController::class, 'login']);

Route::get('/calculate-price', [PricingApiController::class, 'calculatePrice']);
Route::get('/available-slots', [SlotApiController::class, 'availableSlots']);
Route::get('/apartments', [CatalogApiController::class, 'apartments']);
Route::get('/bus-stands', [CatalogApiController::class, 'busStands']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/driver/dashboard', [DriverApiController::class, 'dashboard']);
    Route::get('/driver/today-trips', [DriverApiController::class, 'todayTrips']);
    Route::get('/driver/earnings', [DriverApiController::class, 'earnings']);
    Route::post('/driver/start-trip/{id}', [DriverApiController::class, 'startTrip']);
    Route::post('/driver/complete-trip/{id}', [DriverApiController::class, 'completeTrip']);
    Route::post('/driver/toggle-online', [DriverApiController::class, 'toggleOnline']);
});

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/send-otp', [AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'status' => true,
        'message' => 'Success',
        'data' => $request->user(),
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/create-booking', [BookingApiController::class, 'createBooking']);
    Route::put('/modify-booking/{id}', [BookingApiController::class, 'modifyBooking']);
    Route::get('/booking-history', [BookingApiController::class, 'bookingHistory']);
    Route::get('/upcoming-bookings', [BookingApiController::class, 'upcomingBookings']);
    Route::get('/completed-bookings', [BookingApiController::class, 'completedBookings']);
    Route::post('/cancel-booking/{id}', [BookingApiController::class, 'cancelBooking']);

    Route::get('/pre-bookings', [PreBookingApiController::class, 'index']);
    Route::post('/pre-bookings', [PreBookingApiController::class, 'store']);
    Route::post('/pre-bookings/{id}/confirm', [PreBookingApiController::class, 'confirm']);
    Route::post('/pre-bookings/{id}/cancel', [PreBookingApiController::class, 'cancel']);

    Route::get('/subscription-plans', [SubscriptionApiController::class, 'plans']);
    Route::get('/my-subscription', [SubscriptionApiController::class, 'mySubscription']);
    Route::post('/purchase-subscription', [SubscriptionApiController::class, 'purchase']);
});
