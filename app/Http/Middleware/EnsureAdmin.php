<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('login'))->with('error', 'Please log in to access this area.');
        }

        if (! $request->user()->isAdmin()) {
            abort(403, 'Unauthorized. Administrator access required.');
        }

        return $next($request);
    }
}
