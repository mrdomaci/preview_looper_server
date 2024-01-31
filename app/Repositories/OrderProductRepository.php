<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Connector\OrderDetailResponse;
use App\Connector\OrderResponse;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;

class OrderProductRepository {
    
    public function createOrUpdate(OrderResponse $orderResponse, OrderDetailResponse $orderDetailResponse, Client $client, Order $order): void
    {
        $product = Product::where('client_id', $client->getAttribute('id'))
                    ->where('guid', $orderDetailResponse->getProductGuid())
                    ->where('parent_product_id', null)
                    ->first();
        OrderProduct::where('client_id', $client->getAttribute('id'))
            ->where('order_guid', $orderResponse->getGuid())
            ->where('product_guid', $orderDetailResponse->getProductGuid())
            ->delete();
        for ($j = 1; $j <= (int) $orderDetailResponse->getAmount(); $j++) {
            $orderProduct = new OrderProduct();
            $orderProduct->setAttribute('client_id', $client->getAttribute('id'));
            $orderProduct->setAttribute('order_id', $order->getAttribute('id'));
            $orderProduct->setAttribute('order_guid', $orderResponse->getGuid());
            $orderProduct->setAttribute('product_guid', $orderDetailResponse->getProductGuid());
            if ($product !== null) {
                $orderProduct->setAttribute('product_id', $product->getAttribute('id'));
            }
            $orderProduct->save();
        }
    }
}