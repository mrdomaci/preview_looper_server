<?php

namespace App\Http\Controllers;

use App\Exceptions\DataNotFoundException;
use App\Helpers\ResponseHelper;
use App\Models\Client;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ImageController extends Controller
{
    public function list(string $clientId, string $productGUIDs): JsonResponse
    {
        try {
            $client = Client::where('eshop_id', (int) $clientId)->first();
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Client not found'], 404);
        }
        $productGUIDs = explode('|', $productGUIDs);
        try {
            $products = Product::select('id')->where('client_id', $client->getAttribute('id'))->whereIn('guid', $productGUIDs)->get();
            $productIDs = [];
            foreach ($products->toArray() as $product) {
                $productIDs[] = (int) $product['id'];
            }
            $images = Image::where('client_id', $client->getAttribute('id'))->whereIn('product_id', $productIDs)->get();
        } catch (\Throwable $e) {
            throw new DataNotFoundException($e);
        }
        $imagesResponse = ResponseHelper::getImageResponseArray($images, $client->getAttribute('eshop_id'));

        return response()->json($imagesResponse);
    }
}
