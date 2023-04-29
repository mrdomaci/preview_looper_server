<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Connector\EshopResponse;
use App\Connector\ProductImageResponse;
use App\Connector\ProductResponse;
use App\Connector\Request;
use App\Models\Client;

class ConnectorHelper
{
    /**
     * @return array<ProductResponse>
     */
    public static function getProducts(Client $client): array
    {
        $request = new Request($client);
        $request->getProducts();
        $response = $request->send();
        return $response->getProducts();
    }

    /**
     * @return array<ProductImageResponse>
     */
    public static function getProductImages(Client $client, string $productGuid): array
    {
        $request = new Request($client);
        $request->getProductImages($productGuid, 'shop');
        $response = $request->send();
        return $response->getProductImages();
    }

    public static function getEshop(Client $client): EshopResponse
    {
        $request = new Request($client);
        $request->getEshop();
        $response = $request->send();
        return $response->getEshop();
    }
}