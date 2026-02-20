<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckForceLogout
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'force_logout:' . $request->session()->getId();
        $store = Cache::store('file');

        if ($store->has($key)) {
            $store->forget($key);
            session()->flash('force_logout', true);
        }

        return $next($request);
    }
}
