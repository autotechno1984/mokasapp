<?php

use Illuminate\Support\Facades\Route;

$centralDomains = config('tenancy.central_domains', []);

/**
 * Sesuaikan path panel/portal tenant kamu.
 * Kalau Filament default: admin
 * Kalau kamu set custom di PanelProvider: ganti di sini.
 */
defined('ADMIN_PATH') || define('ADMIN_PATH', 'admin');

/**
 * Domain publik utama tempat route bernama (landing, dashboard, settings)
 * didaftarkan. WAJIB dipakai untuk penamaan supaya route('dashboard') dsb.
 * menghasilkan URL domain asli (mis. mokasapp.com), BUKAN entri pertama
 * central_domains yang kebetulan 127.0.0.1.
 */
$appDomain = config('app.domain');
$namedDomain = in_array($appDomain, $centralDomains, true)
    ? $appDomain
    : ($centralDomains[array_key_first($centralDomains)] ?? null);

/**
 * Register central (non-tenant) routes only for configured central domains.
 */
foreach ($centralDomains as $domain) {
    $isPrimary = ($domain === $namedDomain);

    Route::domain($domain)->group(function () use ($isPrimary) {

        // Landing page
        $landingRoute = Route::get('/', function () {
            return view('landing');
        });

        /**
         * Central dashboard route:
         * - wajib login
         * - kalau user punya tenant + subdomain, redirect ke tenant domain panel
         * - kalau tidak ada tenant, logout dan balik login
         */
        $dashboardRoute = Route::get('/dashboard', function () {
            $user = auth()->user();

            // Pastikan relasi tenant ada / loaded
            $subdomain = $user?->tenant?->subdomain;

            // Domain utama app, contoh: mokasapp.com
            $appDomain = config('app.domain');

            if ($subdomain && $appDomain) {
                // scheme ambil dari APP_URL kalau ada, fallback dari request
                $scheme = parse_url((string) config('app.url'), PHP_URL_SCHEME) ?: request()->getScheme();

                // port hanya dipakai kalau bukan 80/443 (buat local dev)
                $port = request()->getPort();
                $portSegment = $port && ! in_array($port, [80, 443], true) ? ":{$port}" : '';

                // Redirect ke tenant panel (bukan /dashboard)
                return redirect()->to(sprintf(
                    '%s://%s.%s%s/%s',
                    $scheme,
                    $subdomain,
                    $appDomain,
                    $portSegment,
                    ADMIN_PATH
                ));
            }

            // Kalau user tidak punya tenant/subdomain: paksa logout supaya tidak nyangkut session
            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return redirect()->route('login');
        })->middleware(['auth', 'verified']);

        // Naming hanya di domain publik utama biar gak duplikat & URL benar
        if ($isPrimary) {
            $landingRoute->name('landing');
            $dashboardRoute->name('dashboard');

            // Settings routes bernama hanya sekali
            require __DIR__ . '/settings.php';
        } else {
            // Settings routes tambahan TANPA nama untuk domain lain
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
