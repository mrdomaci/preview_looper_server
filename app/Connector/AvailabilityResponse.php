<?php

declare(strict_types=1);

namespace App\Connector;

class AvailabilityResponse
{
    public function __construct(
        private string $id,
        private string $name,
        private bool $isSystem,
        private ?string $description,
        private ?string $onStockInHours,
        private ?string $deliveryInHours,
        private ?string $color,
        private ?GoogleAvailabilityResponse $googleAvailability,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isSystem(): bool
    {
        return $this->isSystem;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getOnStockInHours(): ?string
    {
        return $this->onStockInHours;
    }

    public function getDeliveryInHours(): ?string
    {
        return $this->deliveryInHours;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getGoogleAvailability(): ?GoogleAvailabilityResponse
    {
        return $this->googleAvailability;
    }
}
