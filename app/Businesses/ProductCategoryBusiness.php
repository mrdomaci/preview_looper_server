<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Models\Client;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;

class ProductCategoryBusiness
{
    private const MAX_ITERATION_COUNT = 10000;
    public function __construct(
        private ProductRepository $productRepository,
        private CategoryRepository $categoryRepository
    ) {
    }

    public function createOrUpdate(Client $client): void
    {
        $lastProductId = 0;
        for ($j = 0; $j < $this->getMaxIterationCount(); $j++) {
            foreach ($this->productRepository->getPastId($client, $lastProductId) as $product) {
                $lastProductId = $product->getId();
                if ($product->getCategoryName() === '') {
                    continue;
                }
                if ($product->getCategoryName() === null) {
                    continue;
                }
                $category = $this->categoryRepository->createOrUpdate($client, $product->getCategoryName());
                $this->productRepository->setProductCategory($product, $category);
            }
        }
    }

    private function getMaxIterationCount(): int
    {
        return self::MAX_ITERATION_COUNT;
    }
}
