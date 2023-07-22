<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Connector\EshopResponse;
use App\Connector\ProductImageResponse;
use App\Connector\ProductResponse;
use App\Connector\Request;
use App\Connector\TemplateIncludeResponse;
use App\Models\Client;
use App\Models\ClientService;

class ConnectorHelper
{
    /**
     * @return array<ProductResponse>
     */
    public static function getProducts(ClientService $clientService, int $page): array
    {
        $request = new Request($clientService);
        $request->getProducts($page);
        $response = $request->send();
        return $response->getProducts();
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

    public static function postTemplateInclude(ClientService $clientService, string $body): TemplateIncludeResponse
    {
        $request = new Request($clientService);
        $request->postTemplateInclude($body);
        $response = $request->send();
        return $response->postTemplateIncluded();
    }
}