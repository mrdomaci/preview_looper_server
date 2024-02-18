<?php

declare(strict_types=1);

namespace App\Connector;

class OrderStatusListResponse
{
    /**
     * @param array<int, OrderStatusResponse> $orderStatuses
     */
    public function __construct(
        private array $orderStatuses = []
    ) {
    }
    /**
     * @return array<int, OrderStatusResponse>
     */
    public function getOrderStatuses(): array
    {
        return $this->orderStatuses;
    }
    public function addOrderStatus(OrderStatusResponse $orderStatus): void
    {
        $this->orderStatuses[] = $orderStatus;
    }
}
