<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Connector\Fio\LicenseListResponse;
use App\Connector\Fio\Request as FioRequest;
use App\Connector\Shoptet\AvailabilityListResponse;
use App\Connector\Shoptet\EshopResponse;
use App\Connector\Shoptet\OrderDetailListResponse;
use App\Connector\Shoptet\OrderListResponse;
use App\Connector\Shoptet\ProductDetailResponse;
use App\Connector\Shoptet\ProductFilter;
use App\Connector\Shoptet\ProductListResponse;
use App\Connector\Shoptet\Request;
use App\Connector\Shoptet\TemplateIncludeResponse;
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
        $response = $request->sendShoptet();
        return $response->getProducts();
    }

    public static function getProductDetail(ClientService $clientService, string $productGuid): ?ProductDetailResponse
    {
        $request = new Request($clientService);
        $request->getProductDetail($productGuid);
        $response = $request->sendShoptet();
        return $response->getProductDetails();
    }

    public static function getEshop(ClientService $clientService): EshopResponse
    {
        $request = new Request($clientService);
        $request->getEshop();
        $response = $request->sendShoptet();
        return $response->getEshop();
    }

    public static function getOrders(ClientService $clientService, int $page, ?DateTime $dateLastSynced): ?OrderListResponse
    {
        $request = new Request($clientService);
        $request->getOrders($page, $dateLastSynced);
        $response = $request->sendShoptet();
        return $response->getOrders();
    }

    public static function getOrderDetail(ClientService $clientService, string $code): ?OrderDetailListResponse
    {
        $request = new Request($clientService);
        $request->getOrderDetail($code);
        $response = $request->sendShoptet();
        return $response->getOrderDetails();
    }

    public static function postTemplateInclude(ClientService $clientService, string $body): TemplateIncludeResponse
    {
        $request = new Request($clientService);
        $request->postTemplateInclude($body);
        $response = $request->sendShoptet();
        return $response->postTemplateIncluded();
    }

    public static function getAvailabilities(ClientService $clientService): ?AvailabilityListResponse
    {
        $request = new Request($clientService);
        $request->getAvailabilities();
        $response = $request->sendShoptet();
        return $response->getAvailabilities();
    }

    public static function getLicense(DateTime $from, DateTime $to): ?LicenseListResponse
    {
        $request = new FioRequest();
        $request->getLicense($from, $to);
        $response = $request->sendFio();
        return $response->getLicense();
    }
}
