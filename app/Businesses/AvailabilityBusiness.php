<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Connector\Shoptet\AvailabilityListResponse;
use App\Connector\Shoptet\AvailabilityResponse;
use App\Enums\AvailabilityLevelEnum;
use App\Models\ClientService;
use App\Repositories\AvailabilityRepository;

class AvailabilityBusiness
{
    public function __construct(
        private AvailabilityRepository $availabilityRepository
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
}
