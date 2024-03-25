<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Connector\ProductCategory;
use App\Connector\ProductDetailResponse;
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

    public function createFromResponse(ProductDetailResponse $productDetailResponse, Product $product): void
    {
        $this->productCategoryRepository->clear($product);
        /** @var ProductCategory $category */
        foreach ($productDetailResponse->getCategories() as $category) {
            $category = $this->categoryRepository->createOrUpdate($product->getClient(), $category->getName(), $category->getGuid());
            $this->productCategoryRepository->create($product, $category);
        }
    }
}
