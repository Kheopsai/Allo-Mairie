<?php

namespace App\Models;

use App\Services\Payments\Stripe\StripeService;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class Product extends Model
{

    use CentralConnection;

    protected $fillable= ['name','description','user_id','payment_id','sku','value'];

    public StripeService $stripe;

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->stripe = new StripeService;
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stripe->retrievePrice($this->attributes['payment_id'])->unit_amount / 100,
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
