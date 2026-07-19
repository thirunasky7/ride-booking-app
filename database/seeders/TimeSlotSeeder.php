<?php

namespace Database\Seeders;

use App\Models\TimeSlot;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        $slots = ['06:30:00', '07:30:00', '08:30:00', '17:30:00', '18:30:00', '19:30:00'];

        foreach ($slots as $slot) {
            TimeSlot::firstOrCreate(['slot_time' => $slot], ['status' => 1]);
        }
    }
}
