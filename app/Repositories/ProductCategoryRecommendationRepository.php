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
            'product_guid' => $product->getGuid(),
            'category_guid' => $category?->getGuid(),
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
                    $join->on('pcr.category_guid', '=', 'pc.category_guid')
                    ->where('pcr.client_id', $client->getId());
                }
            )
            ->where('pc.product_guid', $product->getGuid())
            ->whereNotIn('pcr.product_guid', function ($query) use ($client) {
                $query->select('product_guid')
                    ->from('product_category_recommendations')
                    ->where('client_id', $client->getId())
                    ->where('is_forbidden', true);
            })
            ->select('pcr.product_guid', 'pcr.priority')
            ->orderBy('pcr.priority', 'DESC')
            ->limit($maxResults)
            ->pluck('pcr.priority', 'pcr.product_guid')
            ->toArray();
    }
}
