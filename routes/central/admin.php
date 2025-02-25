<?php

use Illuminate\Support\Facades\Route;


Route::namespace('Backend')->middleware(['auth'])->group(function(){
    Route::get('dashboard',Dashboard::class)->name('dashboard');

    Route::namespace('User')->prefix('user')->as('user.')->group(function(){
        Route::get('',Index::class)->name('index');
    });

    Route::namespace('Metropole')->prefix('metropole')->as('metropole.')->group(function(){
        Route::get('',Index::class)->name('index');
        Route::get('/create',Create::class)->name('create');
        Route::get('/update/{metropole}',Update::class)->name('update');
    });

    Route::namespace('Tenant')->prefix('tenant')->as('tenant.')->group(function(){
        Route::get('',Index::class)->name('index');
    });

    Route::namespace('Plan')->prefix('plan')->as('plan.')->group(function (){
        Route::get('',Index::class)->name('index');
        Route::get('/create',Create::class)->name('create');
        Route::get('/update/{plan}',Update::class)->name('update');

    });

    Route::namespace('Role')->prefix('role')->as('role.')->group(function(){
        Route::get('',Index::class)->name('index');
    });

    Route::namespace('Permission')->prefix('permission')->as('permission.')->group(function(){
        Route::get('',Index::class)->name('index');
    });

    Route::namespace('Setting')->prefix('setting')->as('setting.')->group(function (){
        Route::get('',Index::class)->name('index');
    });
});
