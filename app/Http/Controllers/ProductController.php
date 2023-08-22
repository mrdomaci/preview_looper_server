<?php

namespace App\Http\Controllers;

use App\Enums\ClientServiceStatusEnum;
use App\Helpers\WebHookHelper;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function update(string $eshopID, string $productGUID): JsonResponse
    { 
        $client = Client::where('eshop_id', (int) $eshopID)->first();
        if ($client === null) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        $clientService =  ClientService::where('client_id', $client->getAttribute('id'))->where('service_id', Service::DYNAMIC_PREVIEW_IMAGES)->where('status', ClientServiceStatusEnum::ACTIVE)->first();
        if ($clientService === null) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $product = Product::where('client_id', $client->getAttribute('id'))->where('guid', $productGUID)->first();
        if ($product !== null) {
            WebHookHelper::jenkinsWebhookProduct($client->getAttribute('id'), $productGUID);
            return response()->json(['success' => true], 201);
        }
        return response()->json(['success' => true]);
    }
}