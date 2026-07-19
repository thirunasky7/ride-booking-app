<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = Driver::all();

        foreach ($drivers as $index => $driver) {
            Vehicle::firstOrCreate(
                ['vehicle_number' => 'SHUTTLE-'.str_pad($index + 1, 3, '0', STR_PAD_LEFT)],
                [
                    'driver_id' => $driver->id,
                    'vehicle_name' => 'Shuttle Van '.($index + 1),
                    'capacity' => 6,
                    'status' => 1,
                ]
            );
        }
    }
}
