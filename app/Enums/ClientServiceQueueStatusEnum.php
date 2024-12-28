<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Service;

enum ClientServiceQueueStatusEnum: string
{
    use BaseEnumTrait;
    case CLIENTS = 'clients';
    case PRODUCTS = 'products';
    case ORDERS = 'orders';
    case DONE = 'done';
    case AVAILABILITIES = 'availabilities';
    case API = 'api';
    case SNAPSHOT_PRODUCTS = 'snapshot_products';
    case SNAPSHOT_ORDERS = 'snapshot_orders';
    case CACHE = 'cache';
    case DB_PRODUCTS = 'db_products';
    case DB_ORDERS = 'db_orders';
    case LICENSE = 'license';

    public function next(Service $service): self
    {
        if ($service->isDynamicPreviewImages()) {
            return match ($this->name) {
                self::CLIENTS->name => self::PRODUCTS,
                self::PRODUCTS->name => self::API,
                self::API->name => self::SNAPSHOT_PRODUCTS,
                self::SNAPSHOT_PRODUCTS->name => self::DB_PRODUCTS,
                self::DB_PRODUCTS->name => self::CACHE,
                default => self::DONE,
            };
        }
        return match ($this->name) {
            self::CLIENTS->name => self::PRODUCTS,
            self::PRODUCTS->name => self::AVAILABILITIES,
            self::AVAILABILITIES->name => self::ORDERS,
            self::ORDERS->name => self::API,
            self::API->name => self::SNAPSHOT_PRODUCTS,
            self::SNAPSHOT_PRODUCTS->name => self::DB_PRODUCTS,
            self::DB_PRODUCTS->name => self::SNAPSHOT_ORDERS,
            self::SNAPSHOT_ORDERS->name => self::DB_ORDERS,
            self::DB_ORDERS->name => self::LICENSE,
            default => self::DONE,
        };
    }

    public function isDone(): bool
    {
        return $this->name === self::DONE->name;
    }
}
