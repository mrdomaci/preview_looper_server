<?php

namespace App\Http\Controllers;

use App\Exceptions\DataNotFoundException;
use App\Models\Image;
use Illuminate\Http\JsonResponse;

class ImageController extends Controller
{
    public function list(string $clientId, string $productIds): JsonResponse
    {
        $productIds = explode('|', $productIds);
        try {
            $images = Image::select('product_id', 'url')->where('client_id', (int) $clientId)->whereIn('product_id', $productIds)->get();
        } catch (\Throwable $e) {
            throw new DataNotFoundException($e);
        }

        return response()->json($images);
    }
}
