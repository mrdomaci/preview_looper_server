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

class ProductRepository {
    /**
     * @param Client $client
     * @param int $lastProductId
     * @param int $iterationCount
     * @return Collection<Product>
     */
    public function getPastId(Client $client, int $lastProductId, int $iterationCount = 100): Collection {
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
    public function getParentsInIds(array $ids, int $iterationCount = 4): Collection {
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
    public function getActivesByClient(Client $client): Collection {
        return Product::where('client_id', $client->getId())->where('active', true)->get();
    }

    public function setProductCategory(Product $product, Category $category): void {
        $product->setAttribute('category_id', $category->getId());
        $product->save();
    }

    public function delete(Product $product): void {
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
        $product = Product::where('client_id', $client->getId())->where('guid', $productResponse->getGuid())->first();
        if ($product === null) {
            $product = new Product();
            $product->setAttribute('guid', $productResponse->getGuid());
            $product->setAttribute('client_id', $client->getId());
            $product->setAttribute('active', true);
            $product->save();
        } else if ($product->isActive() === false) {
            $product->setAttribute('active', true);
            $product->save();
        }
    }

    public function updateDetailFromResponse(Product $product, ProductDetailResponse $productDetailResponse): void
    {
        $product->setAttribute('name', $productDetailResponse->getName());
        $product->setAttribute('perex', $productDetailResponse->getPerex());
        $product->setAttribute('category', $productDetailResponse->getDefaultCategory()?->getName());
        $product->setAttribute('producer', $productDetailResponse->getBrand()?->getName());
        $product->setAttribute('url', $productDetailResponse->getUrl());
        $product->setAttribute('price', PriceHelper::getUnfiedPriceString($productDetailResponse->getVariants()));
        $product->setAttribute('image_url', $productDetailResponse->getImageUrl());
        $product->save();
    }

    public function deleteCollection(Collection $products): void
    {
        $productIDs = $products->pluck('id')->toArray();
        Product::whereIn('id', $productIDs)->delete();
    }

    public function createOrUpdateVariantFromResponse(ProductVariantResponse $variant, Product $product): void
    {
        $productVariant = Product::where('parent_product_id', $product->getId())->where('code', $variant->getCode())->first();
        if ($productVariant === null) {
            $productVariant = Product::clone($product);
        }
        $productVariant->setAttribute('name', $variant->getName());
        $productVariant->setAttribute('code', $variant->getCode());
        $productVariant->setAttribute('active', true);
        $productVariant->setAttribute('availability', $variant->getAvailability());
        $productVariant->setAttribute('availability_id', $variant->getAvailabilityId());
        $productVariant->setAttribute('stock', $variant->getStock());
        $productVariant->setAttribute('unit', $variant->getUnit());
        $productVariant->setAttribute('price', Currency::formatPrice((string)$variant->getPrice(), $variant->getCurrencyCode()));
        $productVariant->setAttribute('image_url', $variant->getImage());
        $productVariant->setAttribute('url', $product->getUrl());
        $productVariant->save();
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