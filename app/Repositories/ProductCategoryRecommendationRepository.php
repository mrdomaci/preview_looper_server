<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductCategoryRecommendation;
use Illuminate\Support\Facades\DB;

class ProductCategoryRecommendationRepository
{
    public function get(int $id): ProductCategoryRecommendation
    {
        return ProductCategoryRecommendation::findOrFail($id);
    }
    public function create(Client $client, Product $product, ?Category $category, ?bool $isForbidden = false): ProductCategoryRecommendation
    {
        return ProductCategoryRecommendation::create([
            'product_id' => $product->getId(),
            'category_id' => $category?->getId(),
            'client_id' => $client->getId(),
            'is_forbidden' => $isForbidden,
        ]);
    }

    public function delete(ProductCategoryRecommendation $productCategoryRecommendation): void
    {
        $productCategoryRecommendation->delete();
    }

    /**
     * @param Client $client
     * @param Product $product
     * @param int $maxResults
     * @return array<int>
     */
    public function getByClientProduct(Client $client, Product $product, int $maxResults): array
    {
        return DB::table('product_categories', 'pc')
            ->join(
                'product_category_recommendations as pcr',
                function ($join) use ($client) {
                    $join->on('pcr.category_id', '=', 'pc.category_id')
                    ->where('pcr.client_id', $client->getId());
                })
            ->where('pc.product_id', $product->getId())
            ->whereNotIn('pcr.product_id', function ($query) {
                $query->select('product_id')
                    ->from('product_category_recommendations')
                    ->where('is_forbidden', true);
            })
            ->select('pcr.product_id', 'pcr.priority')
            ->orderBy('pcr.priority', 'DESC')
            ->limit($maxResults)
            ->pluck('pcr.priority', 'pcr.product_id')
            ->toArray();
    }
}
