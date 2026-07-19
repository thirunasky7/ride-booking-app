<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Expire subscriptions past their end date';

    public function handle(SubscriptionService $subscriptionService): int
    {
        $count = $subscriptionService->expireDueSubscriptions();
        $this->info("Expired {$count} subscriptions.");

        return self::SUCCESS;
    }
}
