<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isViewer()) {
            return redirect()->route('tenant.dashboard');
        }

        return $next($request);
    }
}
