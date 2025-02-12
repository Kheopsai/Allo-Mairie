<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Backend')->middleware(['auth'])->group(function (){
    Route::get('dashboard',Dashboard::class)->name('dashboard');

    Route::namespace('Chat')->prefix('chat')->as('chat.')->group(function(){
        Route::get('',Index::class)->name('index');
    });
});
