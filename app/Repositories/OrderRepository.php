<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Connector\OrderResponse;
use App\Models\Client;
use App\Models\Order;

class OrderRepository
{
    
    public function createOrUpdate(OrderResponse $orderResponse, Client $client): Order
    {
        $order = Order::where('client_id', $client->getId())->where('guid', $orderResponse->getGuid())->first();
        if ($order === null) {
            $order = new Order();
        }
     
        $order->setAttribute('client_id', $client->getId());
        $order->setAttribute('guid', $orderResponse->getGuid());
        $order->setAttribute('code', $orderResponse->getCode());
        $order->setAttribute('created_at', $orderResponse->getCreationTime());
        $order->setAttribute('updated_at', $orderResponse->getChangeTime());
        $order->setAttribute('full_name', $orderResponse->getFullName());
        $order->setAttribute('company', $orderResponse->getCompany());
        $order->setAttribute('email', $orderResponse->getEmail());
        $order->setAttribute('phone', $orderResponse->getPhone());
        $order->setAttribute('remark', $orderResponse->getRemark());
        $order->setAttribute('cash_desk_order', $orderResponse->isCashDeskOrder());
        $order->setAttribute('customer_guid', $orderResponse->getCustomerGuid());
        $order->setAttribute('paid', $orderResponse->isPaid());
        $order->setAttribute('foreign_status_id', $orderResponse->getForeignStatusId());
        $order->setAttribute('source', $orderResponse->getSource());
        $order->setAttribute('vat', $orderResponse->getPrice()->getVat());
        $order->setAttribute('to_pay', $orderResponse->getPrice()->getToPay());
        $order->setAttribute('currency_code', $orderResponse->getPrice()->getCurrencyCode());
        $order->setAttribute('with_vat', $orderResponse->getPrice()->getWithVat());
        $order->setAttribute('without_vat', $orderResponse->getPrice()->getWithoutVat());
        $order->setAttribute('exchange_rate', $orderResponse->getPrice()->getExchangeRate());
        $order->setAttribute('payment_method', $orderResponse->getPaymentMethod()?->getGuid());
        $order->setAttribute('shipping', $orderResponse->getShipping()?->getGuid());
        $order->setAttribute('admin_url', $orderResponse->getAdminUrl());
        $order->save();

        return $order;
    }
}
