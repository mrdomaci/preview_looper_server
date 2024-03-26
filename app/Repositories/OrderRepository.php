<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Connector\OrderResponse;
use App\Helpers\StringHelper;
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
            ->setFullName(StringHelper::hash($orderResponse->getFullName()))
            ->setCompany(StringHelper::hash($orderResponse->getCompany()))
            ->setEmail(StringHelper::hash($orderResponse->getEmail()))
            ->setPhone(StringHelper::hash($orderResponse->getPhone()))
            ->setRemark(StringHelper::hash($orderResponse->getRemark()))
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
