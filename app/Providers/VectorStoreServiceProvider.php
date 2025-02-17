<?php

namespace App\Providers;

use App\Facade\VectorStoreFacade;
use Illuminate\Support\ServiceProvider;

class VectorStoreServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        VectorStoreFacade::boot();
    }
}
