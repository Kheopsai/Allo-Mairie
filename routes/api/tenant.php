<?php

use App\Actions\Tenant\Backend\File\Chunk;
use App\Actions\Tenant\Backend\File\Finalize;
use App\Actions\Tenant\Backend\UI\Color;
use App\Http\Middleware\TenantNotFound;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomainOrSubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('primary-color', Color::class);
    Route::post('upload-chunk', Chunk::class);
    Route::post('finalize-upload', Finalize::class);
});
