<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Traits\ApiResponse;
use RuntimeException;

class SubscriptionApiController extends Controller
{
    use ApiResponse;

    public function __construct(protected SubscriptionService $subscriptionService) {}

    public function plans()
    {
        $plans = Subscription::where('status', true)->orderBy('price')->get();

        return $this->success(['plans' => $plans]);
    }

    public function mySubscription()
    {
        $active = $this->subscriptionService->getActiveSubscription(auth()->user());

        return $this->success(['subscription' => $active]);
    }

    public function purchase(SubscriptionRequest $request)
    {
        try {
            $plan = Subscription::findOrFail($request->subscription_id);
            $userSub = $this->subscriptionService->purchase(auth()->user(), $plan);

            return $this->success(
                ['user_subscription' => $userSub->load('subscription')],
                'Subscription purchased successfully.'
            );
        } catch (RuntimeException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
