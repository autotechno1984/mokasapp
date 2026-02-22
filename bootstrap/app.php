<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function ($request) {
            // Kalau request sedang berada di tenant (stancl tenancy)
            if (function_exists('tenant') && tenant()) {
                $central = config('app.domain', 'mokasapp.com');
                $intended = urlencode($request->fullUrl());

                return "https://{$central}/login?intended={$intended}";
            }

            // Central domain: login normal
            return '/login';
        });


        $middleware->web(append: [
            \App\Http\Middleware\CheckForceLogout::class,
        ]);

        $middleware->alias([
            'role.owner' => \App\Http\Middleware\EnsureOwnerRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
