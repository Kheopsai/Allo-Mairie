<?php

namespace App\Providers;

use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class SubscriptionServiceProvider extends ServiceProvider
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
        Cashier::ignoreRoutes();
        Cashier::calculateTaxes();
        Cashier::keepPastDueSubscriptionsActive();
        Cashier::keepIncompleteSubscriptionsActive();
        Cashier::useCustomerModel(Tenant::class);
        Cashier::useSubscriptionModel(Subscription::class);
    }
}
