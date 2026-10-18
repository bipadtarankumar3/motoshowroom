<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of all tenant showroom subscriptions with calculation engine and multi-filtering.
     */
    public function index(Request $request): View
    {
        $query = $this->buildFilteredQuery($request);

        // Sort Order
        $sort = $request->input('sort', 'expiry_asc');
        match ($sort) {
            'expiry_desc' => $query->orderBy('current_period_end', 'desc'),
            'created_desc' => $query->latest('created_at'),
            'newest' => $query->latest('id'),
            default => $query->orderBy('current_period_end', 'asc'),
        };

        $subscriptions = $query->paginate(15)->withQueryString();

        // Financial & Calculation Telemetry
        $allSubscriptions = Subscription::with(['plan'])->get();

        $activeSubs = $allSubscriptions->where('status', SubscriptionStatus::Active)
            ->filter(fn ($s) => $s->current_period_end && $s->current_period_end->isFuture());

        $totalMrr = (float) $activeSubs->sum(function ($sub) {
            if (! $sub->plan) {
                return 0;
            }

            return $sub->billing_interval === 'annual'
                ? ((float) $sub->plan->annual_price / 12)
                : (float) $sub->plan->monthly_price;
        });

        $totalArr = $totalMrr * 12;

        $capturedPayments = (float) Payment::where('status', PaymentStatus::Captured->value)->sum('amount');
        $estimatedFromSubs = (float) $allSubscriptions->sum(function ($sub) {
            if (! $sub->plan) {
                return 0;
            }

            return $sub->billing_interval === 'annual'
                ? (float) $sub->plan->annual_price
                : (float) $sub->plan->monthly_price;
        });
        $totalEarned = max($capturedPayments, $estimatedFromSubs);

        $expiring7DaysSubs = $allSubscriptions->filter(function ($s) {
            return $s->current_period_end
                && $s->current_period_end->isFuture()
                && $s->current_period_end->lte(now()->addDays(7));
        });

        $expiring30DaysSubs = $allSubscriptions->filter(function ($s) {
            return $s->current_period_end
                && $s->current_period_end->isFuture()
                && $s->current_period_end->lte(now()->addDays(30));
        });

        $revenueAtRisk7Days = (float) $expiring7DaysSubs->sum(function ($s) {
            return $s->billing_interval === 'annual'
                ? (float) ($s->plan?->annual_price ?? 0)
                : (float) ($s->plan?->monthly_price ?? 0);
        });

        $revenueAtRisk30Days = (float) $expiring30DaysSubs->sum(function ($s) {
            return $s->billing_interval === 'annual'
                ? (float) ($s->plan?->annual_price ?? 0)
                : (float) ($s->plan?->monthly_price ?? 0);
        });

        $arpu = $activeSubs->count() > 0 ? round($totalMrr / $activeSubs->count()) : 0;

        $stats = [
            'total' => $allSubscriptions->count(),
            'active' => $activeSubs->count(),
            'trial' => $allSubscriptions->where('status', SubscriptionStatus::Trial)->count(),
            'past_due' => $allSubscriptions->where('status', SubscriptionStatus::PastDue)->count(),
            'expired' => $allSubscriptions->filter(fn ($s) => $s->status === SubscriptionStatus::Expired || ($s->current_period_end && $s->current_period_end->isPast()))->count(),
            'expiring_7_days' => $expiring7DaysSubs->count(),
            'expiring_30_days' => $expiring30DaysSubs->count(),
            'auto_renew' => $allSubscriptions->where('auto_renew', true)->count(),
            'annual' => $allSubscriptions->where('billing_interval', 'annual')->count(),
            'monthly' => $allSubscriptions->where('billing_interval', 'monthly')->count(),
        ];

        $financials = [
            'total_earned' => $totalEarned,
            'mrr' => $totalMrr,
            'arr' => $totalArr,
            'arpu' => $arpu,
            'at_risk_7_days' => $revenueAtRisk7Days,
            'at_risk_30_days' => $revenueAtRisk30Days,
        ];

        $plans = SubscriptionPlan::orderBy('name')->get();

        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'financials', 'plans'));
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

    /**
     * Export subscriptions (selected rows or all matching filters) to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = $this->buildFilteredQuery($request);

        // Check if specific rows were selected (via array selected_ids[] or comma-delimited string selected_ids)
        $selectedIds = $request->input('selected_ids');
        if (is_string($selectedIds) && strlen(trim($selectedIds)) > 0) {
            $selectedIds = array_filter(array_map('trim', explode(',', $selectedIds)));
        }

        if (! empty($selectedIds) && is_array($selectedIds)) {
            $query->whereIn('subscriptions.id', $selectedIds);
        }

        // Apply sort order
        $sort = $request->input('sort', 'expiry_asc');
        match ($sort) {
            'expiry_desc' => $query->orderBy('current_period_end', 'desc'),
            'created_desc' => $query->latest('created_at'),
            'newest' => $query->latest('id'),
            default => $query->orderBy('current_period_end', 'asc'),
        };

        $subscriptions = $query->get();

        $filename = 'subscriptions_report_'.date('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($subscriptions) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'Subscription ID',
                'Showroom Name',
                'Showroom Code / Slug',
                'City',
                'Showroom Phone',
                'Showroom Email',
                'Owner Name',
                'Owner Email',
                'Plan Name',
                'Billing Cycle',
                'Plan Price (INR)',
                'Normalized Monthly MRR (INR)',
                'Estimated ARR (INR)',
                'Status',
                'Auto Renew',
                'Period Start Date',
                'Period End / Expiry Date',
                'Days Remaining / Overdue',
                'Last Payment Reference',
                'Last Payment Date',
                'Subscribed Date',
            ]);

            foreach ($subscriptions as $sub) {
                $plan = $sub->plan;
                $showroom = $sub->showroom;
                $owner = $showroom?->ownerMembership?->user;

                $planPrice = $sub->billing_interval === 'annual'
                    ? (float) ($plan?->annual_price ?? 0)
                    : (float) ($plan?->monthly_price ?? 0);

                $normalizedMrr = $sub->billing_interval === 'annual'
                    ? ($planPrice / 12)
                    : $planPrice;

                $isExpired = $sub->isExpired() || ($sub->current_period_end && $sub->current_period_end->isPast());
                $daysRemaining = $sub->days_remaining;

                $expiryStatus = $isExpired
                    ? 'Expired ('.abs($daysRemaining).' days ago)'
                    : ($daysRemaining.' days left');

                fputcsv($handle, [
                    $sub->id,
                    $showroom?->name ?? 'N/A',
                    $showroom?->slug ?? 'N/A',
                    $showroom?->city ?? 'N/A',
                    $showroom?->phone ?? 'N/A',
                    $showroom?->email ?? 'N/A',
                    $owner?->name ?? 'N/A',
                    $owner?->email ?? 'N/A',
                    $plan?->name ?? 'N/A',
                    strtoupper($sub->billing_interval ?? 'MONTHLY'),
                    number_format($planPrice, 2, '.', ''),
                    number_format($normalizedMrr, 2, '.', ''),
                    number_format($normalizedMrr * 12, 2, '.', ''),
                    ucfirst($sub->status->value ?? (string) $sub->status),
                    $sub->auto_renew ? 'Enabled' : 'Disabled',
                    $sub->current_period_start ? $sub->current_period_start->format('Y-m-d') : 'N/A',
                    $sub->current_period_end ? $sub->current_period_end->format('Y-m-d') : 'N/A',
                    $expiryStatus,
                    $sub->last_payment_reference ?? 'N/A',
                    $sub->last_payment_at ? $sub->last_payment_at->format('Y-m-d H:i') : 'N/A',
                    $sub->created_at ? $sub->created_at->format('Y-m-d H:i') : 'N/A',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Build filtered subscription query from request parameters.
     */
    protected function buildFilteredQuery(Request $request)
    {
        $query = Subscription::with([
            'showroom.ownerMembership.user',
            'plan',
            'histories.adminUser',
        ]);

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Plan Filter
        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->input('plan_id'));
        }

        // Billing Interval Filter (monthly / annual)
        if ($request->filled('billing_interval')) {
            $query->where('billing_interval', $request->input('billing_interval'));
        }

        // Auto Renew Filter
        if ($request->filled('auto_renew')) {
            $query->where('auto_renew', $request->input('auto_renew') === '1');
        }

        // Date-Wise Search (from_date, to_date, date_type)
        if ($request->filled('from_date') || $request->filled('to_date')) {
            $dateColumn = match ($request->input('date_type')) {
                'start' => 'current_period_start',
                'created' => 'created_at',
                'payment' => 'last_payment_at',
                default => 'current_period_end',
            };

            if ($request->filled('from_date')) {
                $query->whereDate($dateColumn, '>=', $request->input('from_date'));
            }
            if ($request->filled('to_date')) {
                $query->whereDate($dateColumn, '<=', $request->input('to_date'));
            }
        }

        // Expiry Horizon Dropdown Filter
        if ($request->filled('expiry_range')) {
            $range = $request->input('expiry_range');
            if ($range === '7') {
                $query->where('current_period_end', '>=', now())
                    ->where('current_period_end', '<=', now()->addDays(7));
            } elseif ($range === '14') {
                $query->where('current_period_end', '>=', now())
                    ->where('current_period_end', '<=', now()->addDays(14));
            } elseif ($range === '30') {
                $query->where('current_period_end', '>=', now())
                    ->where('current_period_end', '<=', now()->addDays(30));
            } elseif ($range === 'expired') {
                $query->where(function ($q) {
                    $q->where('status', SubscriptionStatus::Expired)
                        ->orWhere('current_period_end', '<', now());
                });
            }
        }

        // Quick Preset Filters
        $preset = $request->input('preset');
        if ($preset) {
            match ($preset) {
                'expiring_7' => $query->where('current_period_end', '>=', now())
                    ->where('current_period_end', '<=', now()->addDays(7)),
                'expiring_14' => $query->where('current_period_end', '>=', now())
                    ->where('current_period_end', '<=', now()->addDays(14)),
                'expiring_30' => $query->where('current_period_end', '>=', now())
                    ->where('current_period_end', '<=', now()->addDays(30)),
                'expired' => $query->where(function ($q) {
                    $q->where('status', SubscriptionStatus::Expired)
                        ->orWhere('current_period_end', '<', now());
                }),
                'active' => $query->where('status', SubscriptionStatus::Active)
                    ->where('current_period_end', '>', now()),
                'trial' => $query->where('status', SubscriptionStatus::Trial),
                'past_due' => $query->where('status', SubscriptionStatus::PastDue),
                'auto_renew' => $query->where('auto_renew', true),
                'manual' => $query->where('auto_renew', false),
                'annual' => $query->where('billing_interval', 'annual'),
                'monthly' => $query->where('billing_interval', 'monthly'),
                default => null,
            };
        }

        // Global Search (Showroom name, email, slug, owner name, city, payment reference)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('showroom', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhereHas('ownerMembership.user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                })->orWhere('last_payment_reference', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
