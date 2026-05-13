<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\DriverApiController;
use App\Http\Controllers\Api\SlotApiController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::post(
    '/driver/login',
    [DriverApiController::class, 'login']
);

Route::middleware('auth:sanctum')->group(function () {
    Route::get(
        '/driver/dashboard',
        [DriverApiController::class, 'dashboard']
    );
    Route::get(
        '/driver/today-trips',
        [DriverApiController::class, 'todayTrips']
    );
    Route::post(
        '/driver/start-trip/{id}',
        [DriverApiController::class, 'startTrip']
    );
    Route::post(
        '/driver/complete-trip/{id}',
        [DriverApiController::class, 'completeTrip']
    );
    Route::post(
        '/driver/toggle-online',
        [DriverApiController::class, 'toggleOnline']
    );
});
Route::get(
    '/available-slots',
    [SlotApiController::class, 'availableSlots']
);
Route::post('/send-otp',
    [AuthController::class, 'sendOtp']);

Route::post('/verify-otp',
    [AuthController::class, 'verifyOtp']);
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/create-booking',
        [BookingApiController::class, 'createBooking']);

    Route::get('/booking-history',
        [BookingApiController::class, 'bookingHistory']);

});

Route::middleware('auth:sanctum')->group(function () {

    Route::get(
        '/upcoming-bookings',
        [BookingApiController::class, 'upcomingBookings']
    );

    Route::get(
        '/completed-bookings',
        [BookingApiController::class, 'completedBookings']
    );

    Route::post(
        '/cancel-booking/{id}',
        [BookingApiController::class, 'cancelBooking']
    );

});
