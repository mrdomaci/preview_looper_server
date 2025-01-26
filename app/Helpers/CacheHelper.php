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
        $result = [];
        for ($i = 0; $i < 10000; $i++) {
            $products = Product::where('client_id', $clientId)
                ->where('active', 1)
                ->distinct('guid')
                ->select('guid', 'images')
                ->limit(100)
                ->offset($i * 100)
                ->get();

            /** @var Product $product */
            foreach ($products as $product) {
                $result[$product->getGuid()] = $product->getImages();
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
