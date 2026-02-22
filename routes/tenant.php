<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureUserBelongsToTenant;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
| Routes ini berlaku untuk tenant domain: {subdomain}.mokasapp.com
| Pastikan central_domains hanya berisi domain pusat (mokasapp.com, www, dll)
*/

Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,     // ✅ penting: subdomain-based tenancy
    PreventAccessFromCentralDomains::class,  // ✅ blok akses dari domain pusat
])->group(function () {

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

        // Dashboard untuk semua user tenant
        Route::view('/dashboard', 'dashboard')->name('tenant.dashboard');

        // Owner only
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
