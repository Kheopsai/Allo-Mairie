<?php

namespace App\Services\Payments\Stripe;

use App\Interface\PaymentInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\Price;
use Stripe\Product;
use Stripe\StripeClient;

class StripeService implements PaymentInterface
{
    public StripeClient $stripe;

    private array $currencySymbols = [
        'usd' => '$',
        'eur' => '€',
        'gbp' => '£',
    ];

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * @throws ApiErrorException
     */
    public function createProduct(string $name, string $description): Product
    {
        return $this->stripe->products->create([
            'name' => $name,
            'description' => $description,
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    public function updateProduct(string $id, string $name, string $description): Product
    {
        return $this->stripe->products->update($id, [
            'name' => $name,
            'description' => $description,
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    public function deleteProduct($id): void
    {
        $this->stripe->products->update($id, [
            'active' => false,
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    public function createPrice(string $currency, float $amount, string $interval, string $product_id)
    {
        return $this->stripe->prices->create([
            'currency' => $currency,
            'unit_amount' => $amount * 100,
            'recurring' => ['interval' => $interval],
            'product' => $product_id,
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    public function retrievePrice(string $id): Price
    {
        return $this->stripe->prices->retrieve($id);
    }
    /**
     * @throws ApiErrorException
     */
    public function deletePrice(string $id): void
    {
        $this->stripe->prices->update($id, [
            'active' => false,
        ]);
    }

    public function getPriceCurrencySymbol(string $currency): string
    {
        try {
            $currencyCode = strtolower($currency);
            if (array_key_exists($currencyCode, $this->currencySymbols)) {
                return $this->currencySymbols[$currencyCode];
            }
            return '?';
        } catch (ApiErrorException $e) {
            return '?';
        }
    }
}
