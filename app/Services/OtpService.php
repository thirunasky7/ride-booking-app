<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public const OTP_LENGTH = 4;
    public const OTP_TTL_MINUTES = 5;
    public const MAX_VERIFY_ATTEMPTS = 5;
    public const RESEND_COOLDOWN_SECONDS = 60;
    public const MAX_SEND_PER_HOUR = 5;

    public function send(string $mobile): void
    {
        $record = Otp::firstOrNew(['mobile' => $mobile]);

        if ($record->exists && $record->last_sent_at) {
            $secondsSince = Carbon::parse($record->last_sent_at)->diffInSeconds(now());
            if ($secondsSince < self::RESEND_COOLDOWN_SECONDS) {
                throw ValidationException::withMessages([
                    'mobile' => 'Please wait before requesting another OTP.',
                ]);
            }
        }

        if ($record->send_count >= self::MAX_SEND_PER_HOUR
            && $record->send_window_started_at
            && Carbon::parse($record->send_window_started_at)->gt(now()->subHour())) {
            throw ValidationException::withMessages([
                'mobile' => 'OTP send limit reached. Try again later.',
            ]);
        }

        if (!$record->send_window_started_at
            || Carbon::parse($record->send_window_started_at)->lte(now()->subHour())) {
            $record->send_count = 0;
            $record->send_window_started_at = now();
        }

        $otp = (string) random_int(
            10 ** (self::OTP_LENGTH - 1),
            (10 ** self::OTP_LENGTH) - 1
        );

        $record->fill([
            'otp' => $otp,
            'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
            'attempts' => 0,
            'last_sent_at' => now(),
        ]);
        $record->send_count = ($record->send_count ?? 0) + 1;
        $record->save();

        // Integrate SMS provider here (MSG91 / Twilio / Fast2SMS).
    }

    public function verify(string $mobile, string $otp): User
    {
        $record = Otp::where('mobile', $mobile)->first();

        if (!$record) {
            throw ValidationException::withMessages(['otp' => 'Invalid OTP.']);
        }

        if ($record->attempts >= self::MAX_VERIFY_ATTEMPTS) {
            throw ValidationException::withMessages(['otp' => 'Too many attempts. Request a new OTP.']);
        }

        if (Carbon::now()->gt($record->expires_at)) {
            throw ValidationException::withMessages(['otp' => 'OTP has expired.']);
        }

        if (!hash_equals($record->otp, $otp)) {
            $record->increment('attempts');
            throw ValidationException::withMessages(['otp' => 'Invalid OTP.']);
        }

        $record->delete();

        return $this->findOrCreateCustomer($mobile);
    }

    public function findOrCreateCustomer(string $mobile): User
    {
        return User::firstOrCreate(
            ['mobile' => $mobile],
            [
                'name' => 'Customer',
                'email' => $mobile.'@shuttle.local',
                'role' => 'customer',
                'password' => Hash::make(Str::random(32)),
            ]
        );
    }
}
