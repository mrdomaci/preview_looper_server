<?php
declare(strict_types=1);

namespace App\Connector;

class OrderPaymentMethodResponse
{
    public function __construct(
        private string $guid,
        private string $name,
    ) {
    }

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function getName(): string
    {
        return $this->name;
    }
}