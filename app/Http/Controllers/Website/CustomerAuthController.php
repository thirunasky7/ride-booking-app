<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Otp;

class CustomerAuthController extends Controller
{
    public function login()
    {
        return view('website.customer.login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([

            'mobile' => 'required|digits:10'

        ]);

        $otp = rand(1000,9999);

        Otp::updateOrCreate(

            [
                'mobile' => $request->mobile
            ],

            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(5)
            ]
        );

        session([
            'mobile' => $request->mobile
        ]);

        return back()->with([
            'otp_sent' => true,
            'otp' => $otp
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([

            'otp' => 'required'

        ]);

        $mobile = session('mobile');

        $otp = Otp::where(
            'mobile',
            $mobile
        )
        ->where(
            'otp',
            $request->otp
        )
        ->first();

        if (!$otp) {

            return back()->withErrors([
                'otp' => 'Invalid OTP'
            ]);
        }

        $user = User::firstOrCreate(

            [
                'mobile' => $mobile
            ],

            [
                'name' => 'Customer',
                'role' => 'customer',
                'password' => bcrypt('123456')
            ]
        );

        auth()->login($user);

        return redirect()->route(
            'customer.dashboard'
        );
    }
}