<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Connector\AvailabilityListResponse;
use App\Connector\AvailabilityResponse;
use App\Enums\AvailabilityLevelEnum;
use App\Models\ClientService;
use App\Repositories\AvailabilityRepository;
use App\Repositories\ProductRepository;

class AvailabilityBusiness
{
    public function __construct(
        private AvailabilityRepository $availabilityRepository,
        private ProductRepository $productRepository
    ) {
    }
    public function createOrUpdateFromResponse(ClientService $clientService, ?AvailabilityListResponse $availabilityListResponse): void
    {
        if ($availabilityListResponse === null) {
            return;
        }
        $client = $clientService->client()->first();
        /** @var AvailabilityResponse $availabilityResponse */
        foreach ($availabilityListResponse->getAvailabilities() as $availabilityResponse) {
            $isOnStock = false;
            $isSoldOutNegativeStockAllowed = false;
            $isSoldOutNegativeStockForbidden = false;
            $level = AvailabilityLevelEnum::UNKNOWN;
            if ($availabilityResponse->getId() === $availabilityListResponse->getOnStockId()) {
                $isOnStock = true;
                $level = AvailabilityLevelEnum::IN_STOCK;
            } elseif ($availabilityResponse->getId() === $availabilityListResponse->getSoldOutNegativeStockAllowedId()) {
                $isSoldOutNegativeStockAllowed = true;
                $level = AvailabilityLevelEnum::SELLABLE;
            } elseif ($availabilityResponse->getId() === $availabilityListResponse->getSoldOutNegativeStockForbiddenId()) {
                $isSoldOutNegativeStockForbidden = true;
                $level = AvailabilityLevelEnum::OUT_OF_STOCK;
            }
            $this->availabilityRepository->createOrUpdateFromResponse(
                $client,
                $availabilityResponse,
                $isOnStock,
                $isSoldOutNegativeStockAllowed,
                $isSoldOutNegativeStockForbidden,
                $level
            );
        }
    }

    public function bindProductAvailabilities(ClientService $clientService): void
    {
        $service = $clientService->service()->first();
        if ($service->isDynamicPreviewImages()) {
            return;
        }
        $client = $clientService->client()->first();
        $availabilities = $this->availabilityRepository->getByClient($client);
        foreach ($availabilities as $availability) {
            $this->productRepository->bulkSetAvailability($availability);
        }
        $isOnStockAvailability = $this->availabilityRepository->getIsOnStockAvailability($client);
        if ($isOnStockAvailability !== null) {
            $this->productRepository->bulkSetIsOnStockAvailability($isOnStockAvailability);
        }
        $isSoldOutNegativeStockForbiddenAvailability = $this->availabilityRepository->getSoldOutNegativeStockForbiddenkAvailability($client);
        if ($isSoldOutNegativeStockForbiddenAvailability !== null) {
            $this->productRepository->bulkSetSoldOutNegativeStockForbiddenAvailability($isSoldOutNegativeStockForbiddenAvailability);
        }
        $isSoldOutNegativeStockAllowedAvailability = $this->availabilityRepository->getSoldOutNegativeStockAllowedAvailability($client);
        if ($isSoldOutNegativeStockAllowedAvailability !== null) {
            $this->productRepository->bulkSetSoldOutNegativeStockAllowedAvailability($isSoldOutNegativeStockAllowedAvailability);
        }
    }
}
