<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Connector\Shoptet\OrderDetailResponse;
use App\Connector\Shoptet\OrderResponse;
use App\Connector\Shoptet\ProductDetailResponse;
use App\Connector\Shoptet\ProductFilter;
use App\Connector\Shoptet\ProductResponse;
use App\Models\ClientService;
use DateTime;

class GeneratorHelper
{
    /**
     * @param ClientService $clientService
     * @param string $productGuid
     * @return ?ProductDetailResponse
     */
    public static function fetchProductDetail(ClientService $clientService, string $productGuid): ?ProductDetailResponse
    {
        return ConnectorHelper::getProductDetail($clientService, $productGuid);
    }

    /**
     * @param ClientService $clientService
     * @param int $page
     * @return iterable<ProductResponse>
     */
    public static function fetchProducts(ClientService $clientService, ?ProductFilter $productFilter, int $page): iterable
    {
        $products = ConnectorHelper::getProducts($clientService, $page, $productFilter);
        if ($products === null) {
            return;
        }
        foreach ($products->getProducts() as $item) {
            yield $item;
        }
    }

    /**
     * @param ClientService $clientService
     * @param DateTime|null $changeFrom
     * @param int $page
     * @return iterable<OrderResponse>
     */
    public static function fetchOrders(ClientService $clientService, int $page, ?DateTime $changeFrom = null): iterable
    {
        $orders = ConnectorHelper::getOrders($clientService, $page, $changeFrom);
        if ($orders === null) {
            return;
        }
        foreach ($orders->getOrders() as $item) {
            yield $item;
        }
    }

    /**
     * @param ClientService $clientService
     * @param string $code
     * @return iterable<OrderDetailResponse>
     */
    public static function fetchOrderDetail(ClientService $clientService, string $code): iterable
    {
        $orderDetail = ConnectorHelper::getOrderDetail($clientService, $code);
        if ($orderDetail === null) {
            return;
        }
        foreach ($orderDetail->getProducts() as $item) {
            yield $item;
        }
    }
}
