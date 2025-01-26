<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Models\Product;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductCategoryRepository;

class ProductCategoryBusiness
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ProductCategoryRepository $productCategoryRepository
    ) {
    }

    /**
     * @param Product $product
     * @param array<string, string> $category
     */
    public function createFromSnapshot(Product $product, array $category): void
    {
        $category = $this->categoryRepository->createOrUpdate($product->getClient(), $category['name'], $category['guid']);
        $this->productCategoryRepository->create($product, $category);
    }
}
