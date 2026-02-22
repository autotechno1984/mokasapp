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

        if (! $user || ! $tenant || (int) $user->tenant_id !== (int) $tenant->getTenantKey()) {
            abort(404);
        }

        return $next($request);
    }
}
