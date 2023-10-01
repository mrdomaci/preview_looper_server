<?php

namespace App\Http\Controllers;

use App\Enums\ClientServiceStatusEnum;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

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
}