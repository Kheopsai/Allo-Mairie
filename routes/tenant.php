<?php

declare(strict_types=1);

use App\Http\Middleware\TenantNotFound;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::post('logout',App\Actions\Auth\Logout::class)->middleware('auth:tenant')->name('logout');
Route::middleware([
    'web',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
    TenantNotFound::class,
])->namespace('App\View\Pages\Tenant')->group(function () {
    foreach (glob(__DIR__ . '/tenant/*.php') as $file) {
        require $file;
    }
});
