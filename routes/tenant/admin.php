<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Backend')->middleware(['auth:tenant'])->group(function (){
    Route::get('dashboard',Dashboard::class)->name('dashboard');

    Route::namespace('Chat')->prefix('chat')->as('chat.')->group(function(){
        Route::get('',Index::class)->name('index')->middleware('can:viewAny,App\Models\Chat');
    });
    Route::namespace('Source')->prefix('source')->as('source.')->group(function (){
        Route::get('',Index::class)->name('index');
    });

    Route::namespace('Hub')->prefix('hub')->as('hub.')->group(function (){
        Route::get('',Index::class)->name('index')->middleware('can:viewAny,App\Modeles\Hub');
        Route::get('/show/{hub}',Show::class)->name('show')->middleware('can:viewAny,hub');
    });
    Route::namespace('Settings')->prefix('settings')->as('settings.')->group(function(){
        Route::get('',Index::class)->name('index');
    } );
});
