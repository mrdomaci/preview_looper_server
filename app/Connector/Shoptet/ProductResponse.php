<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

use DateTime;

class ProductResponse
{
    public function __construct(
        private string $guid,
        private ?DateTime $creationTime,
        private ?DateTime $changeTime,
        private ?string $name,
        private ?float $voteAverageScore,
        private ?int $voteCount,
        private ?string $type,
        private ?string $visibility,
        private ?ProductCategory $defaultCategory,
        private ?string $url,
        private ?string $supplier,
        private ?ProductBrand $brand,
    ) {
    }
    public function getGuid(): string
    {
        return $this->guid;
    }

    public function getCreationTime(): ?DateTime
    {
        return $this->creationTime;
    }

    public function getChangeTime(): ?DateTime
    {
        return $this->changeTime;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getVoteAverageScore(): ?float
    {
        return $this->voteAverageScore;
    }

    public function getVoteCount(): ?int
    {
        return $this->voteCount;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function getDefaultCategory(): ?ProductCategory
    {
        return $this->defaultCategory;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getSupplier(): ?string
    {
        return $this->supplier;
    }

    public function getBrand(): ?ProductBrand
    {
        return $this->brand;
    }
}
