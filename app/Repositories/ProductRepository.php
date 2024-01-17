<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository {
    /**
     * @param int $currentClientId
     * @param int $lastProductId
     * @param int $iterationCount
     * @return Collection<Product>
     */
    public function getProductsPastId(int $currentClientId, int $lastProductId, int $iterationCount = 100): Collection {
        return Product::where('client_id', $currentClientId)
        ->where('active', true)
        ->where('id', '>', $lastProductId)
        ->limit($iterationCount)
        ->get();
    }

    public function setProductCategory(Product $product): void {
        $categoryName = $product->getAttribute('category');
        if ($categoryName === null) {
            return;
        }
        $category = Category::createOrUpdate($product->getAttribute('client_id'), $categoryName);
        $product->setAttribute('category', $category->getAttribute('id'));
        $product->save();
    }
}