<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_deducts_rides_on_booking(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $plan = Subscription::create([
            'name' => 'Test',
            'price' => 100,
            'ride_limit' => 5,
            'validity_days' => 30,
            'status' => true,
        ]);

        $sub = app(SubscriptionService::class)->purchase($user, $plan);

        $this->assertEquals(5, $sub->remaining_rides);

        app(SubscriptionService::class)->deductRide($user);

        $this->assertEquals(4, $sub->fresh()->remaining_rides);
    }

    public function test_expired_subscription_is_not_active(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        UserSubscription::create([
            'user_id' => $user->id,
            'subscription_id' => Subscription::create([
                'name' => 'Expired', 'price' => 50, 'ride_limit' => 1,
                'validity_days' => 1, 'status' => true,
            ])->id,
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDay(),
            'remaining_rides' => 0,
            'status' => 'active',
        ]);

        $this->assertNull(app(SubscriptionService::class)->getActiveSubscription($user));
    }
}
