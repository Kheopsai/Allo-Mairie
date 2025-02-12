<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Auth')->prefix('auth')->group(function () {
    Route::get('login',Login::class)->middleware('guest')->name('login');
});
