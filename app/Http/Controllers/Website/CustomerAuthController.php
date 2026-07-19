<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Http\Requests\OtpSendRequest;
use App\Http\Requests\OtpVerifyRequest;
use App\Services\OtpService;

class CustomerAuthController extends Controller
{
    public function __construct(protected OtpService $otpService) {}

    public function login()
    {
        return view('website.customer.login');
    }

    public function sendOtp(OtpSendRequest $request)
    {
        $this->otpService->send($request->mobile);

        session(['mobile' => $request->mobile]);

        return back()->with('otp_sent', true);
    }

    public function verifyOtp(OtpVerifyRequest $request)
    {
        $mobile = session('mobile', $request->mobile);

        $user = $this->otpService->verify($mobile, $request->otp);

        auth()->login($user);

        return redirect()->route('customer.dashboard');
    }
}
