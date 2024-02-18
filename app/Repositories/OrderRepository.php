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
        /** @var Order $order */
        $order->setClient($client)
            ->setGuid($orderResponse->getGuid())
            ->setCode($orderResponse->getCode())
            ->setCreatedAt($orderResponse->getCreationTime())
            ->setUpdatedAt($orderResponse->getChangeTime())
            ->setFullName($orderResponse->getFullName())
            ->setCompany($orderResponse->getCompany())
            ->setEmail($orderResponse->getEmail())
            ->setPhone($orderResponse->getPhone())
            ->setRemark($orderResponse->getRemark())
            ->setCashDeskOrder($orderResponse->isCashDeskOrder())
            ->setCustomerGuid($orderResponse->getCustomerGuid())
            ->setPaid($orderResponse->isPaid())
            ->setForeignStatusId($orderResponse->getForeignStatusId())
            ->setSource($orderResponse->getSource())
            ->setVat($orderResponse->getPrice()->getVat())
            ->setToPay($orderResponse->getPrice()->getToPay())
            ->setCurrencyCode($orderResponse->getPrice()->getCurrencyCode())
            ->setWithVat($orderResponse->getPrice()->getWithVat())
            ->setWithoutVat($orderResponse->getPrice()->getWithoutVat())
            ->setExchangeRate($orderResponse->getPrice()->getExchangeRate())
            ->setPaymentMethod($orderResponse->getPaymentMethod()?->getGuid())
            ->setShipping($orderResponse->getShipping()?->getGuid())
            ->setAdminUrl($orderResponse->getAdminUrl())
            ->save();

        return $order;
    }
}
