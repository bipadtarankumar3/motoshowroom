<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of all tenant showroom subscriptions.
     */
    public function index(Request $request): View
    {
        $query = Subscription::with(['showroom', 'plan', 'histories.adminUser']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->input('plan_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('showroom', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->latest('current_period_end')->paginate(15)->withQueryString();

        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', SubscriptionStatus::Active)->count(),
            'trial' => Subscription::where('status', SubscriptionStatus::Trial)->count(),
            'past_due' => Subscription::where('status', SubscriptionStatus::PastDue)->count(),
            'expired' => Subscription::where('status', SubscriptionStatus::Expired)->count(),
        ];

        $plans = SubscriptionPlan::orderBy('name')->get();

        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'plans'));
    }

    /**
     * Manually extend a subscription's current period end date.
     */
    public function extend(
        Request $request,
        Subscription $subscription,
        SubscriptionService $subscriptionService
    ): RedirectResponse {
        $validated = $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:365'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $subscriptionService->extendExpiry(
            subscription: $subscription,
            days: $validated['days'],
            reason: $validated['reason'],
            adminUserId: auth()->id()
        );

        return redirect()->back()
            ->with('success', "Subscription for '{$subscription->showroom->name}' extended by {$validated['days']} days.");
    }

    /**
     * Administratively override and switch showroom subscription plan.
     */
    public function changePlan(
        Request $request,
        Subscription $subscription,
        SubscriptionService $subscriptionService
    ): RedirectResponse {
        $validated = $request->validate([
            'plan_id' => ['required', 'exists:subscription_plans,id'],
        ]);

        $newPlan = SubscriptionPlan::findOrFail($validated['plan_id']);

        $subscriptionService->changePlan(
            subscription: $subscription,
            newPlan: $newPlan,
            adminUserId: auth()->id()
        );

        return redirect()->back()
            ->with('success', "Showroom '{$subscription->showroom->name}' switched to plan '{$newPlan->name}'.");
    }

    /**
     * Administratively cancel a showroom subscription.
     */
    public function cancel(
        Request $request,
        Subscription $subscription,
        SubscriptionService $subscriptionService
    ): RedirectResponse {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'immediately' => ['nullable'],
        ]);

        $subscriptionService->cancel(
            subscription: $subscription,
            reason: $validated['reason'],
            immediately: $request->has('immediately'),
            adminUserId: auth()->id()
        );

        return redirect()->back()
            ->with('success', "Subscription for '{$subscription->showroom->name}' has been cancelled.");
    }
}
