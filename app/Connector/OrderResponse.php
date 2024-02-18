<?php

declare(strict_types=1);

namespace App\Connector;

use DateTime;

class OrderResponse
{
    public function __construct(
        private string $code,
        private string $guid,
        private DateTime $creationTime,
        private ?DateTime $changeTime,
        private string $fullName,
        private ?string $company,
        private string $email,
        private ?string $phone,
        private ?string $remark,
        private bool $cashDeskOrder,
        private ?string $customerGuid,
        private bool $paid,
        private string $foreignStatusId,
        private string $source,
        private OrderPriceResponse $price,
        private ?OrderPaymentMethodResponse $paymentMethod,
        private ?OrderShippingResponse $shipping,
        private string $adminUrl,
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getGuid(): string
    {
        return $this->guid;
    }

    public function getCreationTime(): DateTime
    {
        return $this->creationTime;
    }

    public function getChangeTime(): ?DateTime
    {
        return $this->changeTime;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function isCashDeskOrder(): bool
    {
        return $this->cashDeskOrder;
    }

    public function getCustomerGuid(): ?string
    {
        return $this->customerGuid;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function getForeignStatusId(): string
    {
        return $this->foreignStatusId;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getPrice(): OrderPriceResponse
    {
        return $this->price;
    }

    public function getPaymentMethod(): ?OrderPaymentMethodResponse
    {
        return $this->paymentMethod;
    }

    public function getShipping(): ?OrderShippingResponse
    {
        return $this->shipping;
    }

    public function getAdminUrl(): string
    {
        return $this->adminUrl;
    }
}
