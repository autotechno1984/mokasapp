<?php

use Illuminate\Support\Facades\Route;

$centralDomains = config('tenancy.central_domains', []);

// Register routes with names only on the first domain to avoid duplicate name errors
foreach ($centralDomains as $index => $domain) {
    Route::domain($domain)->group(function () use ($index) {
        $route = Route::get('/', function () {
            return view('landing');
        });

        $dashboardRoute = Route::get('dashboard', function () {
            $user = auth()->user();
            $subdomain = $user?->tenant?->subdomain;
            $appDomain = config('app.domain');

            if ($subdomain && $appDomain) {
                $scheme = parse_url((string) config('app.url'), PHP_URL_SCHEME) ?: request()->getScheme();
                $port = request()->getPort();
                $portSegment = $port && ! in_array($port, [80, 443], true) ? ":{$port}" : '';

                return redirect()->to(sprintf('%s://%s.%s%s/dashboard', $scheme, $subdomain, $appDomain, $portSegment));
            }

            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login');
        })->middleware(['auth', 'verified']);

        if ($index === 0) {
            $route->name('landing');
            $dashboardRoute->name('dashboard');
        }

        if ($index === 0) {
            require __DIR__.'/settings.php';
        } else {
            // Register settings routes without names for additional domains
            Route::middleware(['auth'])->group(function () {
                Route::redirect('settings', 'settings/profile');
                Route::livewire('settings/profile', 'pages::settings.profile');
            });

            Route::middleware(['auth', 'verified'])->group(function () {
                Route::livewire('settings/password', 'pages::settings.password');
                Route::livewire('settings/appearance', 'pages::settings.appearance');
                Route::livewire('settings/two-factor', 'pages::settings.two-factor');
            });
        }
    });
}
