<?php

namespace App\Models;

use App\Services\Payments\Stripe\StripeService;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Illuminate\Support\Facades\Log;

class Plan extends Model
{
    use CentralConnection;

    public StripeService $stripe;

    protected $fillable = [
        'name', 'short_description',
        'currency', 'monthly_id', 'yearly_id', 'sku', 'selected', 'archived',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->stripe = new StripeService;
    }

    public function setConnection($name): Plan
    {
        return parent::setConnection($this->getConnectionName());
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class, 'plan_id', 'id');
    }

    protected function monthlyPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stripe->retrievePrice($this->attributes['monthly_id'])->unit_amount / 100,
        )->shouldCache();
    }

    protected function yearlyPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stripe->retrievePrice($this->attributes['yearly_id'])->unit_amount / 100,
        )->shouldCache();
    }
    protected function currencyType(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stripe->getPriceCurrencySymbol($this->attributes['currency']),
        )->shouldCache();
    }

    protected static function boot(): void
    {
        parent::boot();
        static::deleted(function ($plan) {
            $stripe = new StripeService;
            try {
                $stripe->deleteProduct($plan->sku);
            } catch (Exception $exception) {
                Log::error($exception->getMessage());
            }
        });
    }
}
