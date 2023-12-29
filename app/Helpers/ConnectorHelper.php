<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Connector\EshopResponse;
use App\Connector\OrderListResponse;
use App\Connector\OrderStatusListResponse;
use App\Connector\ProductDetailResponse;
use App\Connector\ProductFilter;
use App\Connector\ProductImageResponse;
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

    /**
     * @return array<ProductImageResponse>
     */
    public static function getProductImages(ClientService $clientService, string $productGuid): array
    {
        $request = new Request($clientService);
        $request->getProductImages($productGuid, 'shop');
        $response = $request->send();
        return $response->getProductImages();
    }

    public static function getEshop(ClientService $clientService): EshopResponse
    {
        $request = new Request($clientService);
        $request->getEshop();
        $response = $request->send();
        return $response->getEshop();
    }

    public static function getOrderSatuses(ClientService $clientService): OrderStatusListResponse
    {
        $request = new Request($clientService);
        $request->getOrderStatuses();
        $response = $request->send();
        return $response->getOrderStatuses();
    }

    public static function getOrders(ClientService $clientService, int $page, ?DateTime $dateLastSynced): ?OrderListResponse
    {
        $request = new Request($clientService);
        $request->getOrders($page, $dateLastSynced);
        $response = $request->send();
        return $response->getOrders();
    }

    public static function postTemplateInclude(ClientService $clientService, string $body): TemplateIncludeResponse
    {
        $request = new Request($clientService);
        $request->postTemplateInclude($body);
        $response = $request->send();
        return $response->postTemplateIncluded();
    }
}