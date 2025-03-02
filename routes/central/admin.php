<?php

use Illuminate\Support\Facades\Route;


Route::namespace('Backend')->middleware(['auth'])->group(function(){
    Route::get('dashboard',Dashboard::class)->name('dashboard');

    Route::namespace('User')->prefix('user')->as('user.')->group(function(){
        Route::get('',Index::class)->name('index')->middleware('can:viewAny,App\Models\User');
    });

    Route::namespace('Metropole')->prefix('metropole')->as('metropole.')->group(function(){
        Route::get('',Index::class)->name('index')->middleware('can:viewAny,App\Models\Metropole');
        Route::get('/create',Create::class)->name('create')->middleware('can:create,App\Models\Metropole');
        Route::get('/update/{metropole}',Update::class)->name('update')->middleware('can:update,metropole');
    });

    Route::namespace('Tenant')->prefix('tenant')->as('tenant.')->group(function(){
        Route::get('',Index::class)->name('index')->middleware('can:viewAny,App\Models\Tenant');
    });

    Route::namespace('Plan')->prefix('plan')->as('plan.')->group(function (){
        Route::get('',Index::class)->name('index')->middleware('can:viewAny,App\Models\Plan');
        Route::get('/create',Create::class)->name('create')->middleware('can:create,App\Models\Plan');
        Route::get('/update/{plan}',Update::class)->name('update')->middleware('can:update,plan');

    });

    Route::namespace('Product')->prefix('product')->as('product.')->group(function(){
        Route::get('',Index::class)->name('index');
        Route::get('/create',Create::class)->name('create');
        Route::get('/update/{product}',Update::class)->name('update');
    });

    Route::namespace('Role')->prefix('role')->as('role.')->group(function(){
        Route::get('',Index::class)->name('index')->middleware('can:viewAny,App\Models\Role');
    });

    Route::namespace('Permission')->prefix('permission')->as('permission.')->group(function(){
        Route::get('',Index::class)->name('index')->middleware('can:viewAny,App\Models\Permission');
    });

    Route::namespace('Setting')->prefix('setting')->as('setting.')->group(function (){
        Route::get('',Index::class)->name('index');
    });
});
