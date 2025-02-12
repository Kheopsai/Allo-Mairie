<?php

namespace App\Providers;

use App\Facade\LlmManagerFacade;
use Illuminate\Support\ServiceProvider;

class LlmServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        LlmManagerFacade::boot();
    }
}
