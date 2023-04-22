<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Connector\EshopResponse;
use App\Connector\ProductImageResponse;
use App\Connector\ProductResponse;
use App\Connector\Request;

class ConnectorHelper
{
    /**
     * @return array<ProductResponse>
     */
    public static function getProducts(string $apiAccessToken): array
    {
        $request = new Request($apiAccessToken);
        $request->getProducts();
        $response = $request->send();
        return $response->getProducts();
    }

    /**
     * @return array<ProductImageResponse>
     */
    public static function getProductImages(string $apiAccessToken, string $productGuid): array
    {
        $request = new Request($apiAccessToken);
        $request->getProductImages($productGuid, 'shop');
        $response = $request->send();
        return $response->getProductImages();
    }

    public static function getEshop(string $apiAccessToken): EshopResponse
    {
        $request = new Request($apiAccessToken);
        $request->getEshop();
        $response = $request->send();
        return $response->getEshop();
    }
}