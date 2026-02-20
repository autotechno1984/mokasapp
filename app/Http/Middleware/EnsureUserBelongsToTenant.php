<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserBelongsToTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $tenant = tenant();

        if (! $user || ! $tenant || $user->tenant_id !== $tenant->getTenantKey()) {
            abort(404);
        }

        return $next($request);
    }
}
