<?php

namespace App\Providers;

use App\Models\Metropole;
use App\Policies\CompanyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;

class PolicyServiceProvider extends AuthServiceProvider
{

    protected $policies=[
        Metropole::class => CompanyPolicy::class,
    ];

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
        $this->registerPolicies();
    }
}
