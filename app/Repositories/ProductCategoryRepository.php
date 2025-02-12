<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductCategory;

class ProductCategoryRepository
{
    public function create(Product $product, Category $category): ProductCategory
    {
        return ProductCategory::create([
            'product_guid' => $product->getGuid(),
            'category_guid' => $category->getGuid(),
        ]);
    }

    public function clear(Product $product): void
    {
        ProductCategory::where('product_guid', $product->getGuid())->delete();
    }

    /**
     * @param array<int<0, max>, array<string, mixed>>$productCategories
     */
    public function bulkCreateOrUpdate(array $productCategories): void
    {
        ProductCategory::insertOrIgnore(
            $productCategories,
        );
    }

    /**
     * @param array<string> $productIds
     * @param Client $client
     */
    public function dropForProducts(array $productIds, Client $client): void
    {
        foreach ($productIds as $productId) {
            ProductCategory::where('client_id', $client->getId())->where('product_guid', $productId)->delete();
        }
    }

    /**
     * @param Client $client
     */
    public function deleteByClient(Client $client): void
    {
        ProductCategory::where('client_id', $client->getId())->delete();
    }
}
