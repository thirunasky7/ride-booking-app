<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionEnquiryRequest;
use App\Http\Requests\SubscriptionRequest;
use App\Models\Subscription;
use App\Models\SubscriptionEnquiry;
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

    public function submitEnquiry(SubscriptionEnquiryRequest $request)
    {
        $enquiry = SubscriptionEnquiry::create([
            'user_id' => auth()->id(),
            'subscription_id' => $request->subscription_id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'message' => $request->message,
            'preferred_start_date' => $request->preferred_start_date,
            'status' => 'pending',
        ]);

        return $this->success(['enquiry' => $enquiry], 'Enquiry submitted successfully.');
    }
}
