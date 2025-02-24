<?php

namespace App\Interface;

interface PaymentInterface
{
    public function createProduct(string $name, string $description);

    public function updateProduct(string $id, string $name, string $description);

    public function deleteProduct(string $id);

    public function createPrice(string $currency, float $amount, string $interval, string $product_id);

    public function deletePrice(string $id);
}
