<?php

namespace App\Http\Middleware;

use App\Models\Showroom;
use App\Models\SubscriptionPlan;
use App\Services\SubscriptionService;
use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionActive
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {}

    /**
     * Handle an incoming request to verify active subscription status for showroom back-office operations.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Super Admin bypasses subscription gate
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        $showroom = $this->tenantContext->getTenant() ?? $request->attributes->get('tenant');

        if (! $showroom) {
            $param = $request->route('showroom');
            if ($param instanceof Showroom) {
                $showroom = $param;
            } elseif (is_numeric($param)) {
                $showroom = Showroom::find((int) $param);
            } elseif (is_string($param)) {
                $showroom = Showroom::where('slug', $param)->first();
            }
        }

        if (! $showroom) {
            return $next($request);
        }

        // Allow billing & renewal routes even if expired
        if ($request->routeIs('showrooms.billing.*')) {
            return $next($request);
        }

        $subscription = $showroom->subscription;

        // Auto-provision trial if showroom is active but has no subscription record yet
        if (! $subscription) {
            $defaultPlan = SubscriptionPlan::first();
            if ($defaultPlan && $showroom->isActive()) {
                $subscription = app(SubscriptionService::class)->startTrial($showroom, $defaultPlan);
            } elseif ($showroom->isActive()) {
                // If no plan definitions exist in environment, allow based on active showroom status
                return $next($request);
            }
        }

        if (! $subscription || ! $subscription->isActive()) {
            $statusLabel = $subscription ? $subscription->status->label() : 'No Active Subscription';

            return redirect()->route('showrooms.billing.index', $showroom)
                ->with('error', "Your showroom subscription is currently [{$statusLabel}]. Please select a plan or renew to continue managing operations.");
        }

        return $next($request);
    }
}
