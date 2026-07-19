<?php

namespace Database\Seeders;

use App\Models\BusStand;
use Illuminate\Database\Seeder;

class BusStandSeeder extends Seeder
{
    public function run(): void
    {
        $stands = [
            ['name' => 'Central Bus Terminal', 'address' => 'Downtown Hub', 'latitude' => 28.6400, 'longitude' => 77.2300, 'status' => 1],
            ['name' => 'North City Stand', 'address' => 'North Avenue', 'latitude' => 28.6500, 'longitude' => 77.2400, 'status' => 1],
            ['name' => 'East Gate Stand', 'address' => 'East Expressway', 'latitude' => 28.6600, 'longitude' => 77.2500, 'status' => 1],
        ];

        foreach ($stands as $stand) {
            BusStand::firstOrCreate(['name' => $stand['name']], $stand);
        }
    }
}
