<?php

namespace App\Http\Middleware;

use App\Models\Showroom;
use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyShowroomMembership
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {}

    /**
     * Handle an incoming request to verify showroom tenancy authorization.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'))->with('error', 'Please log in to access this showroom.');
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
            abort(404, 'Showroom not resolved.');
        }

        if (! $user->canAccessTenant($showroom)) {
            abort(403, 'Cross-tenant access denied: You are not authorized to access this showroom.');
        }

        return $next($request);
    }
}
