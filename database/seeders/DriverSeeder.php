<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            ['name' => 'Ravi Kumar', 'mobile' => '9876543210', 'license_number' => 'DL-001234', 'password' => Hash::make('driver123'), 'status' => 1, 'is_online' => true],
            ['name' => 'Suresh Patel', 'mobile' => '9876543211', 'license_number' => 'DL-005678', 'password' => Hash::make('driver123'), 'status' => 1, 'is_online' => false],
            ['name' => 'Amit Singh', 'mobile' => '9876543212', 'license_number' => 'DL-009012', 'password' => Hash::make('driver123'), 'status' => 1, 'is_online' => true],
        ];

        foreach ($drivers as $driver) {
            Driver::firstOrCreate(['mobile' => $driver['mobile']], $driver);
        }
    }
}
