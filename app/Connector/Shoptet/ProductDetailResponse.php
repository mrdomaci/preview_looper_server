<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

use DateTime;

class ProductDetailResponse
{
    private ?string $imageUrl = null;
    /**
     * @param array<ProductImageResponse> $images
     * @param array<ProductVariantResponse> $variants
     * @param array<ProductCategory> $categories
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
        private ?string $perex,
        private ?array $images = [],
        private ?array $variants = [],
        private ?array $categories = []
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

    /**
     * @return array<ProductImageResponse>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return array<ProductVariantResponse>
     */
    public function getVariants(): array
    {
        return $this->variants;
    }

    /**
     * @return array<ProductCategory>
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    public function addImage(ProductImageResponse $image): void
    {
        $this->images[] = $image;
    }

    public function addVariant(ProductVariantResponse $variant): void
    {
        $this->variants[] = $variant;
    }

    public function addCategory(ProductCategory $category): void
    {
        $this->categories[] = $category;
    }

    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }
}
