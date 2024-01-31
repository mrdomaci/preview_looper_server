<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductCategoryRecommendation;
use Illuminate\Database\Eloquent\Collection;

class ProductCategoryRecommendationRepository {
    /**
     * @param Product $product
     * @return Collection<ProductCategoryRecommendation>|null
     */
    public function get(Product $product): ?Collection {
        if ($product->getAttribute('category_id') === null) {
            return null;
        }
        return ProductCategoryRecommendation::where('category_id', $product->getAttribute('category_id'))->orderBy('priority')->take(4)->get();
    }
}