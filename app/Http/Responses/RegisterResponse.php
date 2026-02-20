<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
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
            $scheme = parse_url((string) config('app.url'), PHP_URL_SCHEME) ?: ($request instanceof Request ? $request->getScheme() : 'http');
            $url = sprintf('%s://%s.%s/dashboard', $scheme, $subdomain, $appDomain);

            return redirect()->to($url);
        }

        return redirect()->intended(config('fortify.home'));
    }
}
