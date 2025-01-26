<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Client;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderProductRepository
{
    /**
     * @param Client $client
     * @param Product $product
     * @return array<int>
     */
    public function getByProductClient(Client $client, Product $product): array
    {
        return DB::table('order_products', 'op')
            ->join(
                'order_products as op1',
                'op1.order_guid',
                '=',
                'op.order_guid'
            )
            ->where('op.client_id', $client->getId())
            ->where('op.product_guid', $product->getGuid())
            ->whereNotIn('op1.product_guid', function ($query) use ($client) {
                $query->select('product_guid')
                    ->from('product_category_recommendations', 'pcr')
                    ->where('pcr.is_forbidden', true)
                    ->where('pcr.client_id', $client->getId());
            })
            ->select('op1.product_guid', DB::raw('SUM(op1.amount) as count'))
            ->groupBy('op1.product_guid')
            ->limit(100)
            ->pluck('count', 'op1.product_guid')
            ->toArray();
    }

    /**
     * @param array<int<0, max>, array<string, mixed>>$orderProducts
     */
    public function bulkCreateOrIgnore(array $orderProducts): void
    {
        OrderProduct::insertOrIgnore(
            $orderProducts,
        );
    }

    /**
     * @param Client $client
     */
    public function deleteByClient(Client $client): void
    {
        OrderProduct::where('client_id', $client->getId())->delete();
    }
}
