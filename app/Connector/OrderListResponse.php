<?php
declare(strict_types=1);

namespace App\Connector;

class OrderListResponse
{
    /**
     * @param array<int, OrderResponse> $orders
     */
    public function __construct(
        private int $totalCount,
        private int $page,
        private int $pageCount,
        private int $itemsOnPage,
        private int $itemsPerPage,
        private array $orders = []
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
     * @return array<int, OrderResponse>
     */
    public function getOrders(): array
    {
        return $this->orders;
    }
    /**
     * @param array<int, OrderResponse> $orders
     */
    public function setProducts(array $orders): void
    {
        $this->orders = $orders;
    }
    public function addOrder(OrderResponse $order): void
    {
        $this->orders[] = $order;
    }
}