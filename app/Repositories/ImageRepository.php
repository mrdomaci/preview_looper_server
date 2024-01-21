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
        Image::where('client_id', $client->getAttribute('id'))->delete();
    }

    public function deleteByClientAndProduct(Client $client, Product $product): void
    {
        Image::where('client_id', $client->getAttribute('id'))
            ->where('product_id', $product->getAttribute('id'))->delete();
    }

    public function createOrUpdateFromResponse(ProductImageResponse $productImageResponse, Client $client, Product $product): void
    {
        $image = new Image();
        $hash = $client->getAttribute('id') . '-' . $product->getAttribute('id');
        if ($productImageResponse->getPriority() !== null) {
            $hash .= '-' . $productImageResponse->getPriority();
        }
        $image->setAttribute('hash', $hash);
        $image->setAttribute('client_id', $client->getAttribute('id'));
        $image->setAttribute('product_id', $product->getAttribute('id'));
        $image->setAttribute('name', $productImageResponse->getSeoName());
        $image->setAttribute('priority', $productImageResponse->getPriority());
        $image->save();
    }
}