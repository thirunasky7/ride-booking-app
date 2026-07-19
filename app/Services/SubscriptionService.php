<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SubscriptionService
{
    public function assertCanBook(User $user): void
    {
        $active = $this->getActiveSubscription($user);

        if (!$active) {
            return;
        }

        if ($active->end_date->lt(today())) {
            $active->update(['status' => 'expired']);
            throw new RuntimeException('Your subscription has expired.');
        }

        if (!is_null($active->remaining_rides) && $active->remaining_rides <= 0) {
            throw new RuntimeException('You have used all rides in your subscription plan.');
        }
    }

    public function deductRide(User $user): void
    {
        $active = $this->getActiveSubscription($user);

        if (!$active || is_null($active->remaining_rides)) {
            return;
        }

        $active->decrement('remaining_rides');
    }

    public function purchase(User $user, Subscription $plan): UserSubscription
    {
        if (!$plan->status) {
            throw new RuntimeException('This subscription plan is not available.');
        }

        return DB::transaction(function () use ($user, $plan) {
            UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->update(['status' => 'replaced']);

            return UserSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $plan->id,
                'start_date' => today(),
                'end_date' => today()->addDays($plan->validity_days),
                'remaining_rides' => $plan->ride_limit,
                'status' => 'active',
            ]);
        });
    }

    public function getActiveSubscription(User $user): ?UserSubscription
    {
        return UserSubscription::with('subscription')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>=', today())
            ->latest('id')
            ->first();
    }

    public function expireDueSubscriptions(): int
    {
        return UserSubscription::where('status', 'active')
            ->where('end_date', '<', today())
            ->update(['status' => 'expired']);
    }
}
