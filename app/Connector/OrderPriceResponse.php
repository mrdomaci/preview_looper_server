<?php

declare(strict_types=1);

namespace App\Connector;

class OrderPriceResponse
{
    public function __construct(
        private float $vat,
        private float $toPay,
        private string $currencyCode,
        private float $withVat,
        private float $withoutVat,
        private float $exchangeRate,
    ) {
    }

    public function getVat(): float
    {
        return $this->vat;
    }

    public function getToPay(): float
    {
        return $this->toPay;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getWithVat(): float
    {
        return $this->withVat;
    }

    public function getWithoutVat(): float
    {
        return $this->withoutVat;
    }

    public function getExchangeRate(): float
    {
        return $this->exchangeRate;
    }
}
