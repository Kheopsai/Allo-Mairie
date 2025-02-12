<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            foreach (config('tenancy.central_domains', []) as $domain) {
                Route::middleware('web')
                    ->domain($domain)
                    ->group(base_path('routes/web.php'));
            }
        },
        // web: __DIR__.'/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('tenant', [
            'web',
            InitializeTenancyByDomainOrSubdomain::class,
            PreventAccessFromCentralDomains::class,
        ]);
        $middleware->appendToGroup('universal', []);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
