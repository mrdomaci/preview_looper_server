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
        $clientId = $client->getAttribute('id');
        $lastProductId = 0;
        $result = [];
        for ($i = 0; $i < 10000; $i++) {
            $products = Product::where('client_id', $clientId)
                ->where('id', '>', $lastProductId)
                ->where('active', 1)
                ->limit(1000)
                ->get();

            foreach ($products as $product) {
                $productImages = $product->images()->orderBy('priority', 'ASC')->get();
                $guid = $product->getAttribute('guid');
                foreach ($productImages as $productImage) {
                    if (!isset($result[$guid])) {
                        $result[$guid] = [];
                    }
                    $result[$guid][] = $productImage->getAttribute('name');
                }
                $lastProductId = $product->getAttribute('id');
            }
            if (count($products) < 1000) {
                break;
            }
        }
        $jsonResponse = json_encode($result);
        $filename = '/cache/' . $clientId . '_response.json';
        
        return Storage::put($filename, $jsonResponse);
    }
}