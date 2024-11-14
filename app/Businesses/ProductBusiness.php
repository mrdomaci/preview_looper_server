<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Models\Client;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductBusiness
{
    public function __construct(
        private ProductRepository $productRepository,
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
            $produts = new Collection([['guid' => null, 'name' => __('general.no_products_found')]]);
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
}
