<?php

use App\Actions\Tenant\Backend\File\Chunk;
use App\Actions\Tenant\Backend\File\Finalize;
use App\Actions\Tenant\Backend\UI\Color;
use App\Http\Middleware\TenantNotFound;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;


// Route::group(function () {

    foreach (glob(__DIR__ . '/api/*.php') as $file) {
        require $file;
    }
// });
