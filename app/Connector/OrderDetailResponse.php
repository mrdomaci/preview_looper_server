<?php

declare(strict_types=1);

namespace App\Connector;

class OrderDetailResponse
{
    public function __construct(
        private string $productGuid,
        private float $amount,
    ) {
    }
    public function getProductGuid(): string
    {
        return $this->productGuid;
    }
    public function getAmount(): float
    {
        return $this->amount;
    }
}
