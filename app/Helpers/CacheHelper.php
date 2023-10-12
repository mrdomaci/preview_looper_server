<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
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
        for ($i = 0; $i < 1000; $i++) {
            $products = DB::table('products AS p')
                ->join('images AS i', function ($join) use ($clientId) {
                    $join->on('i.product_id', '=', 'p.id')
                        ->where('p.client_id', '=', $clientId);
                })
                ->where('p.id', '>', $lastProductId)
                ->select('p.guid', 'p.id' , 'i.name')
                ->limit(100)
                ->get();

            foreach ($products as $product) {
                $result[$product->guid][] = $product->name;
                $lastProductId = $product->id;
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