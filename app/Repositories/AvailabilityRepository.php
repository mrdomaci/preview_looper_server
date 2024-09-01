<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Connector\Shoptet\AvailabilityResponse;
use App\Enums\AvailabilityLevelEnum;
use App\Exceptions\DataNotFoundException;
use App\Models\Availability;
use App\Models\Client;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class AvailabilityRepository
{

    public function get(int $id): Availability
    {
        $entity = Availability::find($id);
        if ($entity === null) {
            throw new DataNotFoundException(new Exception('Availability not found id: ' . $id));
        }
        return $entity;
    }

    public function getByForeignId(Client $client, string $foreignId): ?Availability
    {
        return Availability::where('client_id', $client->getId())
            ->where('foreign_id', $foreignId)
            ->first();
    }

    public function createOrUpdateFromResponse(
        Client $client,
        AvailabilityResponse $response,
        bool $isOnStock,
        bool $isSoldOutNegativeStockAllowed,
        bool $isSoldOutNegativeStockForbidden,
        AvailabilityLevelEnum $level
    ): void {
        $availability = Availability::where('client_id', $client->getId())
            ->where('foreign_id', $response->getId())
            ->first();
        if ($availability === null) {
            $availability = new Availability();
            $availability->setClient($client);
            $availability->setForeignId($response->getId());
        }
        $availability->setName($response->getName());
        $availability->setDescription($response->getDescription());
        $availability->setIsSystem($response->isSystem());
        $availability->setOnStockInHours($response->getOnStockInHours());
        $availability->setDeliveryInHours($response->getDeliveryInHours());
        $availability->setColor($response->getColor());
        $availability->setIsOnStock($isOnStock);
        $availability->setIsSoldOutNegativeStockAllowed($isSoldOutNegativeStockAllowed);
        $availability->setIsSoldOutNegativeStockForbidden($isSoldOutNegativeStockForbidden);
        $availability->setLevel($level);
        $availability->save();
    }

    /**
     * @param Client $client
     * @return Collection<Availability>
     */
    public function getByClient(Client $client): Collection
    {
        return Availability::where('client_id', $client->getId())->get();
    }

    public function getIsOnStockAvailability(Client $client): ?Availability
    {
        return Availability::where('client_id', $client->getId())
            ->where('is_on_stock', true)
            ->first();
    }

    public function getSoldOutNegativeStockForbiddenkAvailability(Client $client): ?Availability
    {
        return Availability::where('client_id', $client->getId())
            ->where('is_sold_out_negative_stock_forbidden', true)
            ->first();
    }

    public function getSoldOutNegativeStockAllowedAvailability(Client $client): ?Availability
    {
        return Availability::where('client_id', $client->getId())
            ->where('is_sold_out_negative_stock_allowed', true)
            ->first();
    }
}
