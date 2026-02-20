<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    /**
     * Log the current user out of the application.
     */
    public function __invoke()
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        $scheme = parse_url((string) config('app.url'), PHP_URL_SCHEME) ?: 'http';
        $domain = config('app.domain');

        return redirect()->to("{$scheme}://{$domain}/");
    }
}
