<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::firstOrCreate([], [
            'slot_gap_minutes' => 30,
            'booking_open_time' => '06:00:00',
            'booking_close_time' => '22:00:00',
            'commission_percent' => 10,
            'custom_route_price' => 150,
        ]);
    }
}
