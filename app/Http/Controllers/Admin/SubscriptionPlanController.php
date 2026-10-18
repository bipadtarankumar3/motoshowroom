<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of all subscription tiers.
     */
    public function index(): View
    {
        $plans = SubscriptionPlan::withCount('subscriptions')
            ->orderBy('sort_order')
            ->orderBy('monthly_price')
            ->get();

        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new subscription plan.
     */
    public function create(): View
    {
        return view('admin.plans.create');
    }

    /**
     * Store a newly created subscription plan in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150', 'alpha_dash', 'unique:subscription_plans,slug'],
            'description' => ['nullable', 'string'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'annual_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'trial_days' => ['required', 'integer', 'min:0', 'max:365'],
            'max_staff_accounts' => ['required', 'integer', 'min:1'],
            'max_bike_listings' => ['required', 'integer', 'min:1'],
            'max_monthly_bookings' => ['required', 'integer', 'min:1'],
            'media_storage_mb' => ['required', 'integer', 'min:100'],
            'has_careers_module' => ['nullable'],
            'has_scratch_cards' => ['nullable'],
            'has_360_viewer' => ['nullable'],
            'has_ai_assistant' => ['nullable'],
            'has_custom_domain' => ['nullable'],
            'has_advanced_analytics' => ['nullable'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable'],
        ]);

        $slug = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['name']);

        $plan = SubscriptionPlan::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'monthly_price' => $validated['monthly_price'],
            'annual_price' => $validated['annual_price'],
            'currency' => strtoupper($validated['currency']),
            'trial_days' => $validated['trial_days'],
            'max_staff_accounts' => $validated['max_staff_accounts'],
            'max_bike_listings' => $validated['max_bike_listings'],
            'max_monthly_bookings' => $validated['max_monthly_bookings'],
            'media_storage_mb' => $validated['media_storage_mb'],
            'has_careers_module' => $request->has('has_careers_module'),
            'has_scratch_cards' => $request->has('has_scratch_cards'),
            'has_360_viewer' => $request->has('has_360_viewer'),
            'has_ai_assistant' => $request->has('has_ai_assistant'),
            'has_custom_domain' => $request->has('has_custom_domain'),
            'has_advanced_analytics' => $request->has('has_advanced_analytics'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        AuditLogger::log(
            action: 'subscription_plan.created',
            auditable: $plan,
            oldValues: null,
            newValues: $plan->toArray()
        );

        return redirect()->route('admin.plans.index')
            ->with('success', "Subscription Plan '{$plan->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified subscription plan.
     */
    public function edit(SubscriptionPlan $plan): View
    {
        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified subscription plan in storage.
     */
    public function update(Request $request, SubscriptionPlan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'annual_price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'trial_days' => ['required', 'integer', 'min:0', 'max:365'],
            'max_staff_accounts' => ['required', 'integer', 'min:1'],
            'max_bike_listings' => ['required', 'integer', 'min:1'],
            'max_monthly_bookings' => ['required', 'integer', 'min:1'],
            'media_storage_mb' => ['required', 'integer', 'min:100'],
            'has_careers_module' => ['nullable'],
            'has_scratch_cards' => ['nullable'],
            'has_360_viewer' => ['nullable'],
            'has_ai_assistant' => ['nullable'],
            'has_custom_domain' => ['nullable'],
            'has_advanced_analytics' => ['nullable'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable'],
        ]);

        $oldValues = $plan->toArray();

        $plan->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'monthly_price' => $validated['monthly_price'],
            'annual_price' => $validated['annual_price'],
            'currency' => strtoupper($validated['currency']),
            'trial_days' => $validated['trial_days'],
            'max_staff_accounts' => $validated['max_staff_accounts'],
            'max_bike_listings' => $validated['max_bike_listings'],
            'max_monthly_bookings' => $validated['max_monthly_bookings'],
            'media_storage_mb' => $validated['media_storage_mb'],
            'has_careers_module' => $request->has('has_careers_module'),
            'has_scratch_cards' => $request->has('has_scratch_cards'),
            'has_360_viewer' => $request->has('has_360_viewer'),
            'has_ai_assistant' => $request->has('has_ai_assistant'),
            'has_custom_domain' => $request->has('has_custom_domain'),
            'has_advanced_analytics' => $request->has('has_advanced_analytics'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $validated['sort_order'] ?? $plan->sort_order,
        ]);

        AuditLogger::log(
            action: 'subscription_plan.updated',
            auditable: $plan,
            oldValues: $oldValues,
            newValues: $plan->toArray()
        );

        return redirect()->route('admin.plans.index')
            ->with('success', "Subscription Plan '{$plan->name}' updated successfully.");
    }

    /**
     * Toggle active availability of plan.
     */
    public function toggle(SubscriptionPlan $plan): RedirectResponse
    {
        $plan->update(['is_active' => ! $plan->is_active]);

        AuditLogger::log(
            action: 'subscription_plan.toggled',
            auditable: $plan,
            oldValues: ['is_active' => ! $plan->is_active],
            newValues: ['is_active' => $plan->is_active]
        );

        $state = $plan->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.plans.index')
            ->with('success', "Plan '{$plan->name}' has been {$state}.");
    }
}
