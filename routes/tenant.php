<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureUserBelongsToTenant;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;



Route::get('/_env_probe', function () {
    return response()->json([
        'host' => request()->getHost(),
        'app_url' => config('app.url'),
        'app_domain' => config('app.domain'),
        'route_dashboard_url' => route('tenant.dashboard', [], false), // path saja
        'session_domain' => config('session.domain'),
        'fortify_home' => config('fortify.home'),
    ]);
});


Route::middleware([
    'web',
    InitializeTenancyByDomain::class,          // ✅ cocok jika domains.domain = "smb.mokasapp.com"
    PreventAccessFromCentralDomains::class,
])->group(function () {

    /**
     * PROBE (sementara)
     * Kalau ini 404 di smb.mokasapp.com -> tenant routes tidak kepakai (problem loading/routes/cache/mapping/domain)
     * Kalau ini JSON tapi tenant_id null -> tenancy tidak ketemu tenant (mapping domains salah)
     */
    Route::get('/_tenant_probe', function () {
        return response()->json([
            'where' => 'routes/tenant.php',
            'host' => request()->getHost(),
            'tenant_id' => optional(tenant())->getKey(),
        ]);
    });

    // Optional: homepage tenant
    Route::get('/', function () {
        return view('welcome');
    })->name('tenant.home');

    // Share link (public)
    Route::get('/share/{token}', function (string $token) {
        $unit = \App\Models\Unit::where('share_token', $token)
            ->with(['masterbarang.merek', 'masterbarang.tipe', 'unitdetail', 'gambars', 'tenant'])
            ->firstOrFail();

        if ($unit->share_token_expires_at && now()->greaterThan($unit->share_token_expires_at)) {
            $unit->update(['share_token' => null, 'share_token_expires_at' => null]);
            abort(410, 'Link sudah kedaluwarsa.');
        }

        return view('share-unit', compact('unit'));
    })->name('share.unit');

    // Authenticated tenant area
    Route::middleware(['auth', 'verified', EnsureUserBelongsToTenant::class])->group(function () {
        Route::view('/dashboard', 'dashboard')->name('tenant.dashboard');

        Route::middleware('role.owner')->group(function () {
            Route::livewire('/laporan/stock', 'pages::laporan.stock.index')->name('laporan.stock');
            Route::livewire('/unit-create', 'pages::unit.create')->name('unit.create');
            Route::livewire('/biaya', 'pages::biaya.index')->name('biaya.index');
            Route::livewire('/laporan/penjualan', 'pages::laporan.penjualan.index')->name('laporan.penjualan');
            Route::livewire('/laporan/labarugi', 'pages::laporan.labarugi.index')->name('laporan.labarugi');
            Route::livewire('/setting/users', 'pages::setting.user.index')->name('setting.users');
        });
    });
});
