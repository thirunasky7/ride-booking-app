<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::withCount('userSubscriptions')->latest()->paginate(20);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        return view('admin.subscriptions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'ride_limit' => 'nullable|integer|min:1',
            'validity_days' => 'required|integer|min:1',
            'status' => 'required|in:0,1',
        ]);
        $data['status'] = (bool) $data['status'];

        Subscription::create($data);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Plan created successfully.');
    }

    public function edit(Subscription $subscription)
    {
        return view('admin.subscriptions.edit', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'ride_limit' => 'nullable|integer|min:1',
            'validity_days' => 'required|integer|min:1',
            'status' => 'required|in:0,1',
        ]);
        $data['status'] = (bool) $data['status'];

        $subscription->update($data);

        return redirect()->route('admin.subscriptions.index')->with('success', 'Plan updated successfully.');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return back()->with('success', 'Plan deleted.');
    }

    public function subscribers()
    {
        $subscribers = UserSubscription::with(['user', 'subscription'])
            ->latest()
            ->paginate(20);

        return view('admin.subscriptions.subscribers', compact('subscribers'));
    }
}
