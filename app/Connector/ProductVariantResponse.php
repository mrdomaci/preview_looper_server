<?php

declare(strict_types=1);

namespace App\Connector;

class ProductVariantResponse
{
    public function __construct(
        private string $code,
        private ?string $ean,
        private ?float $stock,
        private ?string $unit,
        private ?float $weight,
        private ?float $width,
        private ?float $height,
        private ?float $depth,
        private ?bool $visible,
        private ?int $amountDecimalPlaces,
        private ?float $price,
        private ?bool $includingVat,
        private ?float $vatRate,
        private ?string $currencyCode,
        private ?float $actionPrice,
        private ?float $commonPrice,
        private ?string $availability,
        private string $name,
        private ?string $availabilityId,
        private ?string $image,
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function getStock(): ?float
    {
        return $this->stock;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function getDepth(): ?float
    {
        return $this->depth;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function getAmountDecimalPlaces(): ?int
    {
        return $this->amountDecimalPlaces;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getIncludingVat(): ?bool
    {
        return $this->includingVat;
    }

    public function getVatRate(): ?float
    {
        return $this->vatRate;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function getActionPrice(): ?float
    {
        return $this->actionPrice;
    }

    public function getCommonPrice(): ?float
    {
        return $this->commonPrice;
    }

    public function getAvailability(): ?string
    {
        return $this->availability;
    }

    public function getAvailabilityId(): ?string
    {
        return $this->availabilityId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }
}
