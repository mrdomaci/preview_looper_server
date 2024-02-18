<?php

declare(strict_types=1);

namespace App\Connector;

class ProductListResponse
{
    /**
     * @param array<int, ProductResponse> $products
     */
    public function __construct(
        private int $totalCount,
        private int $page,
        private int $pageCount,
        private int $itemsOnPage,
        private int $itemsPerPage,
        private array $products = []
    ) {
    }
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
    public function getPage(): int
    {
        return $this->page;
    }
    public function getPageCount(): int
    {
        return $this->pageCount;
    }
    public function getItemsOnPage(): int
    {
        return $this->itemsOnPage;
    }
    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }
    /**
     * @return array<int, ProductResponse>
     */
    public function getProducts(): array
    {
        return $this->products;
    }
    /**
     * @param array<int, ProductResponse> $products
     */
    public function setProducts(array $products): void
    {
        $this->products = $products;
    }
    public function addProduct(ProductResponse $product): void
    {
        $this->products[] = $product;
    }
}
