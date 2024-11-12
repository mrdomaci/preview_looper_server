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
            'product_id' => $product->getId(),
            'category_id' => $category->getId(),
        ]);
    }

    public function clear(Product $product): void
    {
        ProductCategory::where('product_id', $product->getId())->delete();
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
        ProductCategory::where('client_id', $client->getId())->whereIn('product_guid', $productIds)->delete();
    }
}
