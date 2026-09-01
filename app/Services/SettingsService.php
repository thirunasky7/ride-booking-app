<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public function get(): Setting
    {
        return Cache::remember('app_settings', now()->addHour(), function () {
            return Setting::firstOrCreate([], [
                'slot_gap_minutes' => 30,
                'booking_open_time' => '06:00:00',
                'booking_close_time' => '22:00:00',
                'commission_percent' => 10,
                'custom_route_price' => 150,
                'razorpay_enabled' => false,
                'site_name' => 'Apartment Shuttle',
            ]);
        });
    }

    public function update(array $data): Setting
    {
        $settings = $this->get();
        $settings->update($data);
        $this->clearCache();

        return $settings->fresh();
    }

    public function clearCache(): void
    {
        Cache::forget('app_settings');
    }

    public function isRazorpayEnabled(): bool
    {
        $settings = $this->get();

        return (bool) $settings->razorpay_enabled
            && !empty($settings->razorpay_key_id)
            && !empty($settings->razorpay_key_secret);
    }

    public function razorpayKeyId(): ?string
    {
        return $this->get()->razorpay_key_id;
    }
}
