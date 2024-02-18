<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Connector\OrderResponse;
use App\Helpers\GeneratorHelper;
use App\Models\ClientService;
use App\Models\Order;
use App\Repositories\OrderProductRepository;

class OrderProductBusiness
{
    public function __construct(private OrderProductRepository $orderProductRepository)
    {
    }
    public function createOrUpdate(ClientService $clientService, OrderResponse $orderResponse, Order $order): void
    {
        $client = $clientService->client()->first();
        foreach (GeneratorHelper::fetchOrderDetail($clientService, $orderResponse->getCode()) as $orderDetailResponse) {
            $this->orderProductRepository->createOrUpdate($orderResponse, $orderDetailResponse, $client, $order);
        }
    }
}
