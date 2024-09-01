<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

class OrderDetailListResponse
{
    /**
     * @param array<int, OrderDetailResponse> $products
     */
    public function __construct(
        private array $products = []
    ) {
    }
    /**
     * @param array<int, OrderDetailResponse> $products
     */
    public function setProducts(array $products): void
    {
        $this->products = $products;
    }
    public function addProduct(OrderDetailResponse $product): void
    {
        $this->products[] = $product;
    }
    /**
     * @return array<int, OrderDetailResponse>
     */
    public function getProducts(): array
    {
        return $this->products;
    }
}
