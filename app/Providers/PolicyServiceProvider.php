<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider;

class PolicyServiceProvider extends AuthServiceProvider
{

    protected $policies=[
        \App\Models\Metropole::class => \App\Policies\CompanyPolicy::class,
        \App\Models\Plan::class => \App\Policies\PlanPolicy::class,
        \App\Models\Role::class => \App\Policies\RolePolicy::class,
        \App\Models\Permission::class => \App\Policies\PermisssionPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Tenant::class => \App\Policies\TenantPolicy::class,
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
