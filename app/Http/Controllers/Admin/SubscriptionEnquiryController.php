<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionEnquiry;
use Illuminate\Http\Request;

class SubscriptionEnquiryController extends Controller
{
    public function index()
    {
        $enquiries = SubscriptionEnquiry::with(['user', 'subscription'])
            ->latest()
            ->paginate(20);

        return view('admin.subscription-enquiries.index', compact('enquiries'));
    }

    public function show(SubscriptionEnquiry $subscription_enquiry)
    {
        $subscription_enquiry->load(['user', 'subscription']);

        return view('admin.subscription-enquiries.show', [
            'enquiry' => $subscription_enquiry,
        ]);
    }

    public function update(Request $request, SubscriptionEnquiry $subscription_enquiry)
    {
        $request->validate([
            'status' => ['required', 'in:pending,contacted,closed'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $subscription_enquiry->update($request->only(['status', 'admin_notes']));

        return redirect()
            ->route('admin.subscription-enquiries.show', $subscription_enquiry)
            ->with('success', 'Enquiry updated successfully.');
    }
}
