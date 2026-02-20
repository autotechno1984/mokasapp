<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureUserBelongsToTenant;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenancyServiceProvider.
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('tenant.home');

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

    Route::middleware(['auth', 'verified', EnsureUserBelongsToTenant::class])->group(function () {
        // Semua user (owner & viewer)
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
