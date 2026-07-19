<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'description' => '20 rides per month for occasional commuters.',
                'price' => 799,
                'ride_limit' => 20,
                'validity_days' => 30,
                'status' => true,
            ],
            [
                'name' => 'Commuter',
                'description' => '60 rides per month for daily commuters.',
                'price' => 1999,
                'ride_limit' => 60,
                'validity_days' => 30,
                'status' => true,
            ],
            [
                'name' => 'Unlimited',
                'description' => 'Unlimited rides for power users.',
                'price' => 3499,
                'ride_limit' => null,
                'validity_days' => 30,
                'status' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Subscription::firstOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
