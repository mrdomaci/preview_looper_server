<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ClientServiceStatusEnum;
use App\Enums\CountryEnum;
use App\Exceptions\DataNotFoundException;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Service;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class ClientServiceRepository
{
    /**
     * @param int $lastId
     * @param Service|null $service
     * @param int|null $clientId
     * @param int $iterationCount
     * @return Collection<ClientService>
     */
    public function getActive(int $lastId, ?Service $service = null, ?int $clientId = null, ?int $iterationCount = 100): Collection
    {
        $query = ClientService::where('status', ClientServiceStatusEnum::ACTIVE)
            ->where('id', '>', $lastId);
        if ($service !== null) {
            $query->where('service_id', $service->getId());
        }

        if ($clientId !== null) {
            $query->where('client_id', $clientId);
        }

        return $query->limit($iterationCount)
            ->get();
    }

    public function getNextForUpdate(Service $service, DateTime $dateLastSync): ClientService
    {
        $q = ClientService::where('service_id', $service->getId())
        ->where('status', ClientServiceStatusEnum::ACTIVE)
        ->where('update_in_process', '=', 0);

        if ($service->isDynamicPreviewImages()) {
            $q->where(function ($query) use ($dateLastSync) {
                $query->where('products_last_synced_at', '<=', $dateLastSync)
                    ->orWhereNull('products_last_synced_at');
            });
        }
        if ($service->isUpsell()) {
            $q->where(function ($query) use ($dateLastSync) {
                $query->where('orders_last_synced_at', '<=', $dateLastSync)
                    ->orWhereNull('orders_last_synced_at');
            });
        }

        return $q->firstOrFail();
    }

    public function hasActiveService(Client $client, Service $service): bool
    {
        return ClientService::where('client_id', $client->getId())
            ->where('service_id', $service->getId())
            ->where('status', ClientServiceStatusEnum::ACTIVE)
            ->exists();
    }

    public function getByClientAndService(Client $client, Service $service): ClientService
    {
        return ClientService::where('client_id', $client->getId())
            ->where('service_id', $service->getId())
            ->where('status', ClientServiceStatusEnum::ACTIVE)
            ->firstOrFail();
    }

    public function updateOrCreate(Client $client, Service $service, string $oAuthAccessToken, CountryEnum $country): ClientService
    {
        try {
            $clientService = ClientService::where('client_id', $client->getId())
            ->where('service_id', $service->getId())
            ->firstOrFail();
        } catch (Throwable $t) {
            $clientService = new ClientService();
            $clientService->setAttribute('client_id', $client->getId());
            $clientService->setAttribute('service_id', $service->getId());
        }
        $clientService->setAttribute('oauth_access_token', $oAuthAccessToken);
        $clientService->setAttribute('status', 'active');
        $clientService->setAttribute('country', $country->value);
        $clientService->setAttribute('update_in_process', false);
        $clientService->save();
        return $clientService;
    }

    public function updateStatus(Client $client, Service $service, ClientServiceStatusEnum $status): void
    {
        try {
            $clientService = ClientService::where('client_id', $client->getId())
            ->where('service_id', $service->getId())
            ->firstOrFail();
        } catch (Throwable $t) {
            throw new DataNotFoundException(new \Exception('ClientService not found for client ' . $client->getId() . ' and service ' . $service->getId()));
        }

        $clientService->setAttribute('status', $status);
        $clientService->save();
    }
}
