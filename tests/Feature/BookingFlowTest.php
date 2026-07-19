<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\BusStand;
use App\Models\RoutePrice;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Services\BookingService;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_booking_with_pricing(): void
    {
        $this->seed();

        $user = User::factory()->create(['role' => 'customer', 'mobile' => '9999999999']);
        $apartment = Apartment::first();
        $busStand = BusStand::first();

        $booking = app(BookingService::class)->create($user, [
            'apartment_id' => $apartment->id,
            'bus_stand_id' => $busStand->id,
            'booking_date' => now()->format('Y-m-d'),
            'slot_time' => '07:30:00',
            'trip_type' => 'apartment_to_busstand',
        ]);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'status' => 'confirmed',
            'booking_type' => 'instant',
        ]);
        $this->assertGreaterThan(0, $booking->price);
    }
}
