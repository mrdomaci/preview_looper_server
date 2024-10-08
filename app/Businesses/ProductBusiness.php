<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Connector\Shoptet\ProductDetailResponse;
use App\Models\Availability;
use App\Models\Client;
use App\Models\Product;
use App\Models\Service;
use App\Repositories\AvailabilityRepository;
use App\Repositories\ClientServiceRepository;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductBusiness
{
    public function __construct(
        private ProductRepository $productRepository,
        private ClientServiceRepository $clientServiceRepository,
        private AvailabilityRepository $availabilityRepository
    ) {
    }

    public function getByGuids(Client $client, string $guids): Collection
    {
        if ($guids === '') {
            return new Collection();
        }
        $guids = explode(',', $guids);
        return $this->productRepository->getParentsByGuids($client, $guids);
    }

    public function getByName(Client $client, string $name): Collection
    {
        $produts = $this->productRepository->getByName($client, $name);
        if ($produts->isEmpty()) {
            /* @phpstan-ignore-next-line */
            $produts = new Collection([['id' => null, 'name' => __('general.no_products_found')]]);
        }
        return $produts;
    }

    /**
     * @param Collection<Product> $products
     * @param string $guid
     * @return Collection<Product>
     */
    public function filterByGuid(Collection $products, string $guid): Collection
    {
        $products->filter(function ($product) use ($guid) {
            /** @var Product $product */
            return $product->getGuid() !== $guid;
        });
        return $products;
    }

    public function createOrUpdateVariants(
        Product $product,
        ProductDetailResponse $productDetailResponse,
        Client $client,
        ?Availability $onStockAvailability,
        ?Availability $soldOutNegativeStockForbidden,
        ?Availability $soldOutNegativeStockAllowed,
    ): void {
        if ($this->clientServiceRepository->hasActiveService($client, Service::getUpsell()) === false) {
            return;
        }
        foreach ($productDetailResponse->getVariants() as $variantResponse) {
            $availability = null;
            if ($variantResponse->getAvailabilityId() !== null) {
                $availability = $this->availabilityRepository->getByForeignId($client, $variantResponse->getAvailabilityId());
            }
            if ($availability === null) {
                if ($variantResponse->getStock() > 0) {
                    $availability = $onStockAvailability;
                } elseif ($variantResponse->isNegativeStockAllowed() === true) {
                    $availability = $soldOutNegativeStockAllowed;
                } else {
                    $availability = $soldOutNegativeStockForbidden;
                }
            }
            $this->productRepository->createOrUpdateVariantFromResponse(
                $variantResponse,
                $product,
                $availability
            );
        }
    }
}
