<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OtpSendRequest;
use App\Http\Requests\OtpVerifyRequest;
use App\Models\Booking;
use App\Models\Otp;
use App\Models\UserSubscription;
use App\Services\OtpService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(protected OtpService $otpService) {}

    public function sendOtp(OtpSendRequest $request)
    {
        try {
            $this->otpService->send($request->mobile);

            return $this->success(null, 'OTP sent successfully.');
        } catch (ValidationException $e) {
            return $this->error($e->getMessage(), 422, $e->errors());
        }
    }

    public function verifyOtp(OtpVerifyRequest $request)
    {
        try {
            $user = $this->otpService->verify($request->mobile, $request->otp);
            $token = $user->createToken('customer-token')->plainTextToken;

            return $this->success([
                'token' => $token,
                'user' => $user,
            ], 'Login successful.');
        } catch (ValidationException $e) {
            return $this->error($e->getMessage(), 401, $e->errors());
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->success(null, 'Logged out successfully.');
    }

    /** Play Store account deletion requirement */
    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            Otp::where('mobile', $user->mobile)->delete();
            UserSubscription::where('user_id', $user->id)->delete();
            Booking::where('user_id', $user->id)->delete();
            $user->delete();
        });

        return $this->success(null, 'Account deleted successfully.');
    }
}
