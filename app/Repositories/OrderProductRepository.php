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
                    ->where('parent_product_id', null)
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
     * @param int $limit
     * @return array<OrderProduct>
     */
    public function getByProduct(Client $client, Product $product, int $limit): array
    {
        return OrderProduct::where('client_id', $client->getId())
            ->where('product_id', $product->getId())
            ->groupBy('order_id')
            ->limit($limit)
            ->get()
            ->toArray();
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
                'op1.order_id',
                '=',
                'op.order_id'
            )
            ->where('op.client_id', $client->getId())
            ->where('op.product_id', $product->getId())
            ->whereNotIn('op1.product_id', function ($query) use ($client) {
                $query->select('product_id')
                    ->from('product_category_recommendations')
                    ->where('is_forbidden', true)
                    ->where('client_id', $client->getId());
            })
            ->select('op1.product_id', DB::raw('COUNT(op1.product_id) as count'))
            ->groupBy('op1.product_id')
            ->limit(100)
            ->pluck('count', 'op1.product_id')
            ->toArray();
    }
}
