<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BillingController extends Controller
{
    /**
     * Display the showroom subscription status, usage quotas, and upgrade plans.
     */
    public function index(Showroom $showroom): View
    {
        $showroom->load(['subscription.plan', 'memberships']);

        $subscription = $showroom->subscription;
        $currentPlan = $subscription?->plan;

        $usage = [
            'staff' => [
                'current' => $showroom->memberships->count(),
                'limit' => $currentPlan?->max_staff_accounts ?? 5,
            ],
            'bikes' => [
                'current' => 0,
                'limit' => $currentPlan?->max_bike_listings ?? 25,
            ],
            'bookings' => [
                'current' => 0,
                'limit' => $currentPlan?->max_monthly_bookings ?? 100,
            ],
            'storage' => [
                'current_mb' => 12,
                'limit_mb' => $currentPlan?->media_storage_mb ?? 1024,
            ],
        ];

        $availablePlans = SubscriptionPlan::active()->get();

        return view('showrooms.billing.index', compact('showroom', 'subscription', 'currentPlan', 'usage', 'availablePlans'));
    }

    /**
     * Change or activate subscription plan for the showroom.
     */
    public function selectPlan(
        Request $request,
        Showroom $showroom,
        SubscriptionService $subscriptionService
    ): RedirectResponse {
        $validated = $request->validate([
            'plan_id' => ['required', 'exists:subscription_plans,id'],
            'billing_interval' => ['required', Rule::in(['monthly', 'annual'])],
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['plan_id']);

        $subscriptionService->subscribe(
            showroom: $showroom,
            plan: $plan,
            billingInterval: $validated['billing_interval'],
            adminUserId: auth()->id()
        );

        return redirect()->route('showrooms.billing.index', $showroom)
            ->with('success', "Congratulations! Your showroom is now active on the '{$plan->name}' plan.");
    }
}
