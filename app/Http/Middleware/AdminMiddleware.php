<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasAnyRole(['Super Admin', 'HRD'])) {
            abort(403, 'Admin access only.');
        }

        return $next($request);
    }
}
