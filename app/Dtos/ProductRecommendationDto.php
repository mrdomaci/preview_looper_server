<?php
declare(strict_types=1);

namespace App\Dtos;

class ProductRecommendationDto {
    public function __construct(
        private string $name,
        private string $price,
        private string $url,
        private string $imageUrl,
        private string $availability,
        private string $code,
        private string $unit,
    ) {
    }
    public function getName(): string {
        return $this->name;
    }

    public function getPrice(): string {
        return $this->price;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function getImageUrl(): string {
        return $this->imageUrl;
    }

    public function getAvailability(): string {
        return $this->availability;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function getUnit(): string {
        return $this->unit;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array {
        return [
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'url' => $this->getUrl(),
            'imageUrl' => $this->getImageUrl(),
            'availability' => $this->getAvailability(),
            'code' => $this->getCode(),
            'unit' => $this->getUnit(),
        ];
    }
}