<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Booking;
use App\Models\BusStand;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\DriverService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DriverApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_cannot_start_unassigned_booking(): void
    {
        $user = User::factory()->create();
        $apartment = Apartment::create(['name' => 'A', 'address' => 'Addr', 'status' => 1]);
        $busStand = BusStand::create(['name' => 'B', 'address' => 'Addr', 'status' => 1]);

        $driver = Driver::create([
            'name' => 'Test Driver',
            'mobile' => '8888888888',
            'license_number' => 'DL-TEST',
            'password' => Hash::make('secret'),
            'status' => 1,
        ]);

        $otherDriver = Driver::create([
            'name' => 'Other',
            'mobile' => '7777777777',
            'license_number' => 'DL-OTHER',
            'password' => Hash::make('secret'),
            'status' => 1,
        ]);

        $vehicle = Vehicle::create([
            'driver_id' => $otherDriver->id,
            'vehicle_name' => 'Van',
            'vehicle_number' => 'TEST-001',
            'capacity' => 4,
            'status' => 1,
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'apartment_id' => $apartment->id,
            'bus_stand_id' => $busStand->id,
            'booking_date' => today(),
            'slot_time' => '08:00:00',
            'trip_type' => 'apartment_to_busstand',
            'status' => 'confirmed',
            'price' => 50,
        ]);

        $this->expectException(\RuntimeException::class);
        app(DriverService::class)->startTrip($driver, $booking->id);
    }
}
