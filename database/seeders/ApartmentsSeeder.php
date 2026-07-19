<?php

namespace Database\Seeders;

use App\Models\Apartment;
use Illuminate\Database\Seeder;

class ApartmentsSeeder extends Seeder
{
    public function run(): void
    {
        $apartments = [
            ['name' => 'Green Valley Apartments', 'address' => 'Sector 12, Main Road', 'latitude' => 28.6139, 'longitude' => 77.2090, 'status' => 1],
            ['name' => 'Sunrise Residency', 'address' => 'Block C, Ring Road', 'latitude' => 28.6200, 'longitude' => 77.2100, 'status' => 1],
            ['name' => 'Lake View Homes', 'address' => 'Near City Mall', 'latitude' => 28.6300, 'longitude' => 77.2200, 'status' => 1],
        ];

        foreach ($apartments as $apartment) {
            Apartment::firstOrCreate(['name' => $apartment['name']], $apartment);
        }
    }
}
