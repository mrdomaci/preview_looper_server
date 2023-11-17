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
                ->join('images AS i', function ($join) {
                    $join->on('i.product_id', '=', 'p.id');
                })
                ->where('p.id', '>', $lastProductId)
                ->where('p.client_id', '=', $clientId)
                ->where('p.active', '=', 1)
                ->select('p.guid', 'p.id' , 'i.name')
                ->limit(10000)
                ->orderBy('p.id')
                ->orderBy('i.priority', 'ASC')
                ->get();

            foreach ($products as $product) {
                if (!isset($result[$product->guid])) {
                    $result[$product->guid] = [];
                }
                $result[$product->guid][] = $product->name;
                $lastProductId = $product->id;
            }
            if (count($products) < 10000) {
                break;
            }
        }
        $jsonResponse = json_encode($result);
        $filename = '/cache/' . $clientId . '_response.json';
        
        return Storage::put($filename, $jsonResponse);
    }
}