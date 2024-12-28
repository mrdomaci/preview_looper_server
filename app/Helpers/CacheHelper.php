<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Client;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class CacheHelper
{
    /**
     * @param Client $client
     * @return bool
     */
    public static function imageResponse(Client $client): bool
    {
        $clientId = $client->getId();
        $lastProductId = 0;
        $result = [];
        for ($i = 0; $i < 10000; $i++) {
            $products = Product::where('client_id', $clientId)
                ->where('id', '>', $lastProductId)
                ->where('active', 1)
                ->distinct('guid')
                ->select('guid', 'images', 'id')
                ->limit(100)
                ->get();

            /** @var Product $product */
            foreach ($products as $product) {
                $result[$product->getGuid()] = $product->getImages();
                $lastProductId = $product->getId();
            }
            if (count($products) < 100) {
                break;
            }
        }
        $jsonResponse = json_encode($result);
        $filename = '/cache/' . $clientId . '_response.json';
        
        return Storage::put($filename, $jsonResponse);
    }
}
