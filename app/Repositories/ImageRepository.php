<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Connector\Shoptet\ProductImageResponse;
use App\Models\Client;
use App\Models\Image;
use App\Models\Product;

class ImageRepository
{
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
        /** @var Image $image */
        $image = new Image();
        $hash = $client->getId() . '-' . $product->getId();
        if ($productImageResponse->getPriority() !== null) {
            $hash .= '-' . $productImageResponse->getPriority();
        }
        $hash .= '-' . $productImageResponse->getSeoName();
        $hash = hash('xxh3', $hash);
        $image->setHash($hash)
            ->setClient($client)
            ->setProduct($product)
            ->setName($productImageResponse->getSeoName())
            ->setPriority($productImageResponse->getPriority())
            ->save();
    }
}
