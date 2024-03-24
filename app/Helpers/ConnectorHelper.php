<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Connector\AvailabilityListResponse;
use App\Connector\EshopResponse;
use App\Connector\OrderDetailListResponse;
use App\Connector\OrderListResponse;
use App\Connector\ProductDetailResponse;
use App\Connector\ProductFilter;
use App\Connector\ProductListResponse;
use App\Connector\Request;
use App\Connector\TemplateIncludeResponse;
use App\Models\ClientService;
use DateTime;

class ConnectorHelper
{
    public static function getProducts(ClientService $clientService, int $page, ?ProductFilter $productFilter): ?ProductListResponse
    {
        $request = new Request($clientService);
        $request->getProducts($page);
        if ($productFilter !== null) {
            $request->addFilterProducts($productFilter);
        }
        $response = $request->send();
        return $response->getProducts();
    }

    public static function getProductDetail(ClientService $clientService, string $productGuid): ?ProductDetailResponse
    {
        $request = new Request($clientService);
        $request->getProductDetail($productGuid);
        $response = $request->send();
        return $response->getProductDetails();
    }

    public static function getEshop(ClientService $clientService): EshopResponse
    {
        $request = new Request($clientService);
        $request->getEshop();
        $response = $request->send();
        return $response->getEshop();
    }

    public static function getOrders(ClientService $clientService, int $page, ?DateTime $dateLastSynced): ?OrderListResponse
    {
        $request = new Request($clientService);
        $request->getOrders($page, $dateLastSynced);
        $response = $request->send();
        return $response->getOrders();
    }

    public static function getOrderDetail(ClientService $clientService, string $code): ?OrderDetailListResponse
    {
        $request = new Request($clientService);
        $request->getOrderDetail($code);
        $response = $request->send();
        return $response->getOrderDetails();
    }

    public static function postTemplateInclude(ClientService $clientService, string $body): TemplateIncludeResponse
    {
        $request = new Request($clientService);
        $request->postTemplateInclude($body);
        $response = $request->send();
        return $response->postTemplateIncluded();
    }

    public static function getAvailabilities(ClientService $clientService): ?AvailabilityListResponse
    {
        $request = new Request($clientService);
        $request->getAvailabilities();
        $response = $request->send();
        return $response->getAvailabilities();
    }
}
