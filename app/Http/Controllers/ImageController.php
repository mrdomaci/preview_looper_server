<?php

namespace App\Http\Controllers;

use App\Enums\ClientServiceStatusEnum;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    public function list(string $eshopID, string $productGUIDs): JsonResponse
    { 
        $client = Client::where('eshop_id', (int) $eshopID)->first();
        if ($client === null) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        $clientService =  ClientService::where('client_id', $client->getAttribute('id'))->where('service_id', Service::DYNAMIC_PREVIEW_IMAGES)->where('status', ClientServiceStatusEnum::ACTIVE)->first();
        if ($clientService === null) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $productGUIDs = explode('|', $productGUIDs);
        $products = Product::where('client_id', $client->getAttribute('id'))->whereIn('guid', $productGUIDs)->get();
        $result = [];
        foreach($productGUIDs as $productGUID) {
            $result[$productGUID] = [];
        }
        foreach ($products as $product) {
            $images = $product->images()->get();
            if (count($images) > 0) {
                $imageLinks = [];
                foreach ($images as $image) {
                    $imageLinks[] = $image->getAttribute('name');
                }
                $result[$product->getAttribute('guid')] = $imageLinks;
            }
        }

        return response()->json($result);
    }

    public function all(string $eshopID, string $moduloCheck): JsonResponse
    {
        if ((int)$eshopID%11 !== (int)$moduloCheck) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $client = Client::where('eshop_id', (int) $eshopID)->first();
        if ($client === null) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        $clientId = $client->getAttribute('id');
        $clientService =  ClientService::where('client_id', $clientId)->where('service_id', Service::DYNAMIC_PREVIEW_IMAGES)->where('status', ClientServiceStatusEnum::ACTIVE)->first();
        if ($clientService === null) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
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

        return response()->json($result);
    }
}