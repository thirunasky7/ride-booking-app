<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            SettingsSeeder::class,
            ApartmentsSeeder::class,
            BusStandSeeder::class,
            DriverSeeder::class,
            VehicleSeeder::class,
            TimeSlotSeeder::class,
            RoutePriceSeeder::class,
            SubscriptionSeeder::class,
        ]);
    }
}
