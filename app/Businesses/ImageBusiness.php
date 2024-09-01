<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Connector\Shoptet\ProductDetailResponse;
use App\Models\Client;
use App\Models\Product;
use App\Models\Service;
use App\Repositories\ClientServiceRepository;
use App\Repositories\ImageRepository;

class ImageBusiness
{
    public function __construct(
        private ImageRepository $imageRepository,
        private ClientServiceRepository $clientServiceRepository,
    ) {
    }

    public function createOrUpdate(Product $product, ProductDetailResponse $productDetailResponse, Client $client): void
    {
        if ($this->clientServiceRepository->hasActiveService($client, Service::getDynamicPreviewImages()) === false) {
            return;
        }
        foreach ($productDetailResponse->getImages() as $imageResponse) {
            $this->imageRepository->createOrUpdateFromResponse($imageResponse, $client, $product);
        }
    }
}
