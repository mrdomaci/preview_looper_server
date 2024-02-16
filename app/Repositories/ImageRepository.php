<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Connector\ProductImageResponse;
use App\Models\Client;
use App\Models\Image;
use App\Models\Product;

class ImageRepository {
    public function deleteByClient(Client $client): void
    {
        Image::where('client_id', $client->getId())->delete();
    }

    public function deleteByClientAndProduct(Client $client, Product $product): void
    {
        Image::where('client_id', $client->getId())
            ->where('product_id', $product->getId())->delete();
    }

    public function createOrUpdateFromResponse(ProductImageResponse $productImageResponse, Client $client, Product $product): void
    {
        $image = new Image();
        $hash = $client->getId() . '-' . $product->getId();
        if ($productImageResponse->getPriority() !== null) {
            $hash .= '-' . $productImageResponse->getPriority();
        }
        $image->setAttribute('hash', $hash);
        $image->setAttribute('client_id', $client->getId());
        $image->setAttribute('product_id', $product->getId());
        $image->setAttribute('name', $productImageResponse->getSeoName());
        $image->setAttribute('priority', $productImageResponse->getPriority());
        $image->save();
    }
}