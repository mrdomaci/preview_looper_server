<?php

declare(strict_types=1);

namespace App\Connector;

class AvailabilityListResponse
{
    /**
     * @param array<AvailabilityResponse> $availabilities
     */
    public function __construct(
        private array $availabilities,
        private ?string $onStockId,
        private ?string $soldOutNegativeStockAllowedId,
        private ?string $soldOutNegativeStockForbiddenId,
    ) {
    }

    public function addAvailability(AvailabilityResponse $availability): void
    {
        $this->availabilities[] = $availability;
    }

    /**
     * @return array<AvailabilityResponse>
     */
    public function getAvailabilities(): array
    {
        return $this->availabilities;
    }

    public function getOnStockId(): ?string
    {
        return $this->onStockId;
    }

    public function getSoldOutNegativeStockAllowedId(): ?string
    {
        return $this->soldOutNegativeStockAllowedId;
    }

    public function getSoldOutNegativeStockForbiddenId(): ?string
    {
        return $this->soldOutNegativeStockForbiddenId;
    }
}
