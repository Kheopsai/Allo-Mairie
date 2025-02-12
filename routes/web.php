<?php

use Illuminate\Support\Facades\Route;


Route::post('logout',App\Actions\Auth\Logout::class)->middleware('auth')->name('logout');
Route::namespace('App\View\Pages\Central')->group(function () {

    foreach (glob(__DIR__ . '/central/*.php') as $file) {
        require $file;
    }
});
