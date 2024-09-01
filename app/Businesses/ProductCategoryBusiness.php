<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Connector\Shoptet\ProductCategory;
use App\Connector\Shoptet\ProductDetailResponse;
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
        /** @var ProductCategory $productCategory */
        foreach ($productDetailResponse->getCategories() as $productCategory) {
            $category = $this->categoryRepository->createOrUpdate($product->getClient(), $productCategory->getName(), $productCategory->getGuid());
            $this->productCategoryRepository->create($product, $category);
        }
    }
}
