<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request): RedirectResponse
    {
        $user = $request instanceof Request ? $request->user() : null;
        $subdomain = $user?->tenant?->subdomain;
        $appDomain = config('app.domain');

        if ($subdomain && $appDomain) {
            // Keluarkan sesi sebelumnya milik user ini (single active session)
            $oldSessionIds = DB::table('sessions')
                ->where('user_id', $user->id)
                ->where('id', '!=', $request->session()->getId())
                ->pluck('id');

            if ($oldSessionIds->isNotEmpty()) {
                $store = Cache::store('file');
                foreach ($oldSessionIds as $sessionId) {
                    $store->put("force_logout:{$sessionId}", true, now()->addMinutes(5));
                }
                DB::table('sessions')->whereIn('id', $oldSessionIds)->delete();
            }

            $scheme = parse_url((string) config('app.url'), PHP_URL_SCHEME) ?: ($request instanceof Request ? $request->getScheme() : 'http');
            $port = $request instanceof Request ? $request->getPort() : null;
            $portSegment = $port && ! in_array($port, [80, 443], true) ? ":{$port}" : '';
            $path = '/dashboard';
            $url = sprintf('%s://%s.%s%s%s', $scheme, $subdomain, $appDomain, $portSegment, $path);

            return redirect()->to($url);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
