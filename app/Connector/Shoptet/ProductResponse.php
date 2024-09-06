<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

use DateTime;

class ProductResponse
{
    /**
     * @param string $guid
     * @param DateTime|null $creationTime
     * @param DateTime|null $changeTime
     * @param string|null $name
     * @param float|null $voteAverageScore
     * @param int|null $voteCount
     * @param string|null $type
     * @param string|null $visibility
     * @param ProductCategory|null $defaultCategory
     * @param string|null $url
     * @param string|null $supplier
     * @param ProductBrand|null $brand
     * @param string|null $perex
     * @param array<string>|null $images
     */
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
        private ?string $perex = null,
        private ?array $images = null,
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

    public function getPerex(): ?string
    {
        return $this->perex;
    }

    public function setPerex(?string $perex): self
    {
        $this->perex = $perex;
        return $this;
    }

    /**
     * @return array<string>|null
     */
    public function getImages(): ?array
    {
        return $this->images;
    }
}
