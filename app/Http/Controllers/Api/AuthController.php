<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Otp;
use Carbon\Carbon;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Send OTP
    |--------------------------------------------------------------------------
    */

    public function sendOtp(Request $request)
    {
        $request->validate([

            'mobile' => 'required|digits:10',

        ]);

        /*
        |--------------------------------------------------------------------------
        | Generate OTP
        |--------------------------------------------------------------------------
        */

        $otp = rand(1000, 9999);

        /*
        |--------------------------------------------------------------------------
        | Delete Old OTP
        |--------------------------------------------------------------------------
        */

        Otp::where('mobile', $request->mobile)->delete();

        /*
        |--------------------------------------------------------------------------
        | Save OTP
        |--------------------------------------------------------------------------
        */

        Otp::create([

            'mobile' => $request->mobile,

            'otp' => $otp,

            'expires_at' => now()->addMinutes(5),

        ]);

        /*
        |--------------------------------------------------------------------------
        | SMS Integration
        |--------------------------------------------------------------------------
        */

        /*
        Use:
        MSG91 / Twilio / Fast2SMS
        */

        return response()->json([

            'status' => true,

            'message' => 'OTP Sent Successfully',

            'otp' => $otp

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Verify OTP
    |--------------------------------------------------------------------------
    */

    public function verifyOtp(Request $request)
    {
        $request->validate([

            'mobile' => 'required',

            'otp' => 'required',

        ]);

        $otpData = Otp::where('mobile', $request->mobile)
            ->where('otp', $request->otp)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Invalid OTP
        |--------------------------------------------------------------------------
        */

        if (!$otpData) {

            return response()->json([

                'status' => false,

                'message' => 'Invalid OTP'

            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Expired OTP
        |--------------------------------------------------------------------------
        */

        if (Carbon::now()->gt($otpData->expires_at)) {

            return response()->json([

                'status' => false,

                'message' => 'OTP Expired'

            ], 401);
        }

        /*
        |--------------------------------------------------------------------------
        | Customer Create/Login
        |--------------------------------------------------------------------------
        */

        $user = User::firstOrCreate(

            [
                'mobile' => $request->mobile
            ],

            [
                'name' => 'Customer',
                'role' => 'customer',
                'password' => bcrypt('123456'),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Create Token
        |--------------------------------------------------------------------------
        */

        $token = $user->createToken('customer-token')
            ->plainTextToken;

        /*
        |--------------------------------------------------------------------------
        | Delete OTP
        |--------------------------------------------------------------------------
        */

        $otpData->delete();

        return response()->json([

            'status' => true,

            'message' => 'Login Success',

            'token' => $token,

            'user' => $user,

        ]);
    }
}