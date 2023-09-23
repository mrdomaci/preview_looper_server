<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Connector\ProductImageResponse;
use App\Connector\ProductResponse;
use App\Models\ClientService;

class GeneratorHelper
{
    /**
     * @param ClientService $clientService
     * @param string $productGuid
     * @return iterable<ProductImageResponse>
     */
    public static function fetchProductImages(ClientService $clientService, string $productGuid): iterable {
        foreach (ConnectorHelper::getProductImages($clientService, $productGuid) as $item) {
            yield $item;
        }
    }

    /**
     * @param ClientService $clientService
     * @param int $page
     * @return iterable<ProductResponse>
     */
    public static function fetchProducts(ClientService $clientService, int $page): iterable {
        $products = ConnectorHelper::getProducts($clientService, $page);
        if ($products === null) {
            return;
        }
        foreach ($products->getProducts() as $item) {
            yield $item;
        }
    }
}