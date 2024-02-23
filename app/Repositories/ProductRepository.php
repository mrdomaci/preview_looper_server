<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Connector\ProductDetailResponse;
use App\Connector\ProductResponse;
use App\Connector\ProductVariantResponse;
use App\Helpers\PriceHelper;
use App\Models\Category;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class ProductRepository
{
    /**
     * @param Client $client
     * @param int $lastProductId
     * @param int $iterationCount
     * @return Collection<Product>
     */
    public function getPastId(Client $client, int $lastProductId, int $iterationCount = 100): Collection
    {
        return Product::where('client_id', $client->getId())
        ->where('active', true)
        ->where('parent_product_id', null)
        ->where('id', '>', $lastProductId)
        ->take($iterationCount)
        ->get();
    }

    /**
     * @param array<int> $ids
     * @param int $iterationCount
     * @return Collection<Product>
     */
    public function getParentsInIds(array $ids, int $iterationCount = 4): Collection
    {
        return Product::where('active', true)
        ->where('parent_product_id', null)
        ->whereIn('id', $ids)
        ->take($iterationCount)
        ->get();
    }

    /**
     * @param Client $client
     * @return Collection<Product>
     */
    public function getActivesByClient(Client $client): Collection
    {
        return Product::where('client_id', $client->getId())->where('active', true)->get();
    }

    public function setProductCategory(Product $product, Category $category): void
    {
        $product->setAttribute('category_id', $category->getId());
        $product->save();
    }

    public function delete(Product $product): void
    {
        Product::where('id', $product->getId())->delete();
    }

    public function deleteByClient(Client $client): void
    {
        Product::where('client_id', $client->getId())->delete();
    }

    /**
     * @param Client $client
     * @param int $lastProductId
     * @return Collection<Product>
     */
    public function getActive(Client $client, int $lastProductId): Collection
    {
        return Product::where('client_id', $client->getId())
                ->where('active', true)
                ->where('id', '>', $lastProductId)
                ->limit(10)
                ->get();
    }

    public function createOrUpdateFromResponse(Client $client, ProductResponse $productResponse): void
    {
        try {
            $product = Product::where('client_id', $client->getId())->where('guid', $productResponse->getGuid())->firstOrFail();
            if ($product->isActive() === false) {
                $product->setActive(true)
                    ->save();
            }
        } catch (Throwable) {
            /** @var Product $product */
            $product = new Product();
            $product->setGuid($productResponse->getGuid())
                ->setClient($client)
                ->setActive(true)
                ->save();
        }
    }

    public function updateDetailFromResponse(Product $product, ProductDetailResponse $productDetailResponse): void
    {
        /** @var Product $product */
        $product->setName($productDetailResponse->getName())
            ->setPerex($productDetailResponse->getPerex())
            ->setCategoryName($productDetailResponse->getDefaultCategory()?->getName())
            ->setProducer($productDetailResponse->getBrand()?->getName())
            ->setUrl($productDetailResponse->getUrl())
            ->setPrice(PriceHelper::getUnfiedPriceString($productDetailResponse->getVariants()))
            ->setImageUrl($productDetailResponse->getImageUrl())
            ->save();
    }

    public function deleteCollection(Collection $products): void
    {
        $productIDs = $products->pluck('id')->toArray();
        $chunks = array_chunk($productIDs, 100);
        foreach ($chunks as $chunk) {
            Product::whereIn('id', $chunk)->delete();
        }
    }

    public function createOrUpdateVariantFromResponse(ProductVariantResponse $variant, Product $product): void
    {
        try {
            $productVariant = Product::where('parent_product_id', $product->getId())->where('code', $variant->getCode())->firstOrFail();
        } catch (Throwable) {
            $productVariant = Product::clone($product);
        }
        /** @var Product $productVariant */
        $productVariant->setName($variant->getName())
            ->setCode($variant->getCode())
            ->setActive(true)
            ->setAvailability($variant->getAvailability())
            ->setAvailabilityId($variant->getAvailabilityId())
            ->setStock($variant->getStock())
            ->setUnit($variant->getUnit())
            ->setPrice(Currency::formatPrice((string)$variant->getPrice(), $variant->getCurrencyCode()))
            ->setImageUrl($variant->getImage())
            ->setUrl($product->getUrl())
            ->save();
    }

    /**
     * @param Client $client
     * @param array<string> $guids
     * @return Collection<Product>
     */
    public function getParentsByGuids(Client $client, array $guids): Collection
    {
        return Product::where('client_id', $client->getId())
            ->where('active', true)
            ->where('parent_product_id', null)
            ->whereIn('guid', $guids)
            ->get();
    }

    public function getByName(Client $client, string $name): Collection
    {
        return Product::where('client_id', $client->getId())
            ->select('id', 'name')
            ->where('active', true)
            ->where('name', 'like', '%' . $name . '%')
            ->where('parent_product_id', null)
            ->limit(5)
            ->get();
    }

    public function getForClient(Client $client, int $id): Product
    {
        return Product::where('client_id', $client->getId())
            ->where('id', $id)
            ->firstOrFail();
    }

    public function getBestVariant(Client $client, int $productId): Product
    {
        return Product::where('client_id', $client->getId())
            ->where('parent_product_id', $productId)
            ->where('active', true)
            ->where('stock', '>', 0)
            ->orderBy('stock', 'desc')
            ->select('name', 'code', 'guid', 'price', 'availability', 'image_url', 'url', 'unit')
            ->firstOrFail();
    }
}
