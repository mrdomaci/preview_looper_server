<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Connector\Shoptet\OrderDetailResponse;
use App\Connector\Shoptet\OrderResponse;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderProductRepository
{
    
    public function createOrUpdate(OrderResponse $orderResponse, OrderDetailResponse $orderDetailResponse, Client $client, Order $order): void
    {
        $product = Product::where('client_id', $client->getId())
                    ->where('guid', $orderDetailResponse->getProductGuid())
                    ->first();
        OrderProduct::where('client_id', $client->getId())
            ->where('order_guid', $orderResponse->getGuid())
            ->where('product_guid', $orderDetailResponse->getProductGuid())
            ->delete();
        for ($j = 1; $j <= (int) $orderDetailResponse->getAmount(); $j++) {
            /** @var OrderProduct $orderProduct */
            $orderProduct = new OrderProduct();
            $orderProduct->setClient($client)
                ->setOrder($order)
                ->setOrderGuid($orderResponse->getGuid())
                ->setProductGuid($orderDetailResponse->getProductGuid())
                ->setProduct($product)
                ->save();
        }
    }

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
            ->select('op1.product_guid', DB::raw('COUNT(op1.product_guid) as count'))
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
}
