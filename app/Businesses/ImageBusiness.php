<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Connector\Shoptet\ProductDetailResponse;
use App\Connector\Shoptet\ProductImageResponse;
use App\Models\Product;

class ImageBusiness
{
    public function createOrUpdate(Product $product, ProductDetailResponse $productDetailResponse): void
    {
        $images = [];
        $productImages = $productDetailResponse->getImages();
        usort($productImages, function ($a, $b) {
            return $a->getPriority() <=> $b->getPriority();
        });
        /** @var ProductImageResponse $imageResponse */
        foreach ($productImages as $imageResponse) {
            $images[] = $imageResponse->getSeoName();
        }
        $product->setImages($images);
        $product->save();
    }
}
