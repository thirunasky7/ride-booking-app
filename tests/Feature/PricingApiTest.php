<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\BusStand;
use App\Models\RoutePrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_price_returns_estimated_fare(): void
    {
        $this->seed();

        $apartment = Apartment::first();
        $busStand = BusStand::first();

        $response = $this->getJson('/api/calculate-price?'.http_build_query([
            'apartment_id' => $apartment->id,
            'bus_stand_id' => $busStand->id,
            'booking_date' => now()->format('Y-m-d'),
            'slot_time' => '07:30:00',
        ]));

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonStructure(['data' => ['estimated_fare', 'booking_type']]);
    }
}
