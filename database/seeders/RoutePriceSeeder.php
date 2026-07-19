<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\BusStand;
use App\Models\RoutePrice;
use Illuminate\Database\Seeder;

class RoutePriceSeeder extends Seeder
{
    public function run(): void
    {
        $apartments = Apartment::all();
        $busStands = BusStand::all();

        foreach ($apartments as $apartment) {
            foreach ($busStands as $busStand) {
                RoutePrice::firstOrCreate(
                    [
                        'apartment_id' => $apartment->id,
                        'bus_stand_id' => $busStand->id,
                    ],
                    [
                        'base_price' => 49.00,
                        'peak_price' => 69.00,
                        'holiday_price' => 79.00,
                        'peak_from' => '08:00:00',
                        'peak_to' => '10:00:00',
                        'status' => 1,
                    ]
                );
            }
        }
    }
}
