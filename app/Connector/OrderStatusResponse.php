<?php
declare(strict_types=1);

namespace App\Connector;

use DateTime;

class OrderStatusResponse
{
    public function __construct(
        private int $id,
        private string $name,
        private bool $system,
        private int $order,
        private bool $markAsPaid,
        private string $color,
        private string $backgroundColor,
        private bool $changeOrderItems,
        private bool $stockClaimResolved,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isSystem(): bool
    {
        return $this->system;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function isMarkAsPaid(): bool
    {
        return $this->markAsPaid;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getBackgroundColor(): string
    {
        return $this->backgroundColor;
    }

    public function isChangeOrderItems(): bool
    {
        return $this->changeOrderItems;
    }

    public function isStockClaimResolved(): bool
    {
        return $this->stockClaimResolved;
    }
}