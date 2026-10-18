<?php

namespace App\Http\Middleware;

use App\Models\Showroom;
use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantContext
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {}

    /**
     * Handle an incoming request and resolve active showroom tenant.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $showroom = null;

        // 1. Check if route parameter 'showroom' is already bound or supplied as slug/ID
        $param = $request->route('showroom');

        if ($param instanceof Showroom) {
            $showroom = $param;
        } elseif (is_numeric($param)) {
            $showroom = Showroom::find((int) $param);
        } elseif (is_string($param)) {
            $showroom = Showroom::where('slug', $param)
                ->orWhere('custom_domain', $param)
                ->first();
        }

        // 2. Subdomain resolution if route param wasn't present
        if (! $showroom) {
            $host = $request->getHost();
            $platformDomain = config('showroom.platform_domain', 'localhost');

            if (str_ends_with($host, '.'.$platformDomain)) {
                $subdomain = substr($host, 0, -strlen('.'.$platformDomain));
                if (! in_array($subdomain, config('showroom.tenancy.reserved_slugs', []), true)) {
                    $showroom = Showroom::where('slug', $subdomain)->first();
                }
            } else {
                // Check custom domain mapping
                $showroom = Showroom::where('custom_domain', $host)
                    ->whereNotNull('domain_verified_at')
                    ->first();
            }
        }

        if (! $showroom) {
            abort(404, 'Motorcycle showroom could not be located.');
        }

        // Set tenant context in container
        $this->tenantContext->setTenant($showroom);
        $request->attributes->set('tenant', $showroom);

        return $next($request);
    }
}
