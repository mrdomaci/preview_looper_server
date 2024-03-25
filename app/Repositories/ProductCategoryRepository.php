<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
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
}
