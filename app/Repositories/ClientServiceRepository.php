<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ClientServiceQueueStatusEnum;
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
     * @param Client|null $client
     * @param int $iterationCount
     * @return Collection<ClientService>
     */
    public function list(int $lastId, ?Service $service = null, ?Client $client = null, ?int $iterationCount = 100): Collection
    {
        $query = ClientService::where('id', '>', $lastId);
        if ($service !== null) {
            $query->where('service_id', $service->getId());
        }

        if ($client !== null) {
            $query->where('client_id', $client->getId());
        }

        return $query->limit($iterationCount)
            ->get();
    }

    /**
     * @param int $lastId
     * @param Service|null $service
     * @param Client|null $client
     * @param int $iterationCount
     * @return Collection<ClientService>
     */
    public function getActive(int $lastId, ?Service $service = null, ?Client $client = null, ?int $iterationCount = 100): Collection
    {
        $query = ClientService::where('status', ClientServiceStatusEnum::ACTIVE)
            ->where('id', '>', $lastId);
        if ($service !== null) {
            $query->where('service_id', $service->getId());
        }

        if ($client !== null) {
            $query->where('client_id', $client->getId());
        }

        return $query->limit($iterationCount)
            ->get();
    }

    /**
     * @param DateTime $dateLastSync
     * @param Service|null $service
     * @param int|null $limit
    * @return Collection<ClientService>
    */
    public function getNextForUpdate(DateTime $dateLastSync, ?Service $service, ?int $limit = 1): Collection
    {
        $q = ClientService::where('status', ClientServiceStatusEnum::ACTIVE)
        ->where('update_in_process', '=', 0)
        ->where('queue_status', ClientServiceQueueStatusEnum::DONE)
        ->where(function ($query) use ($dateLastSync) {
            $query->where('synced_at', '<=', $dateLastSync)
              ->orWhereNull('synced_at');
        })->orderBy('webhooked_at', 'asc');
        if ($service !== null) {
            $q->where('service_id', $service->getId());
        }
        return $q->limit($limit)->get();
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

    public function updateStatus(Client $client, Service $service, ClientServiceStatusEnum $status): ClientService
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
        return $clientService;
    }

    public function getByVariableSymbol(string $variableSymbol): ?ClientService
    {
        $year = substr($variableSymbol, 0, 2);
        $clientServiceId = substr($variableSymbol, 3);
        $clientServiceId = ltrim($clientServiceId, '0');
        $clientService = ClientService::find((int) $clientServiceId);
        if ($clientService === null) {
            return null;
        }
        if ($clientService->getCreatedAt()->format('y') !== $year) {
            return null;
        }
        return $clientService;
    }

    public function get(int $id): ClientService
    {
        $entity = ClientService::find($id);
        if ($entity === null) {
            throw new DataNotFoundException(new \Exception('ClientService not found id: ' . $id));
        }
        return $entity;
    }
    /**
     * @param ClientServiceQueueStatusEnum $status
     * @param int|null $limit
    * @return Collection<ClientService>
    */
    public function getForUpdate(ClientServiceQueueStatusEnum $status, ?int $limit = 1): Collection
    {
        return ClientService::where('queue_status', $status)
            ->where('update_in_process', false)
            ->where('status', ClientServiceStatusEnum::ACTIVE)
            ->where('webhooked_at', '>=', new DateTime('-12 hours'))
            ->orderBy('webhooked_at', 'asc')
            ->limit($limit)
            ->get();
    }

    public function getDeleted(?int $limit = 10): Collection
    {
        return ClientService::where('status', ClientServiceStatusEnum::DELETED)
            ->whereNot('queue_status', ClientServiceQueueStatusEnum::DELETED)
            ->whereNotIn('client_id', function ($query) {
                $query->select('client_id')
                    ->from('client_services')
                    ->whereNot('status', ClientServiceStatusEnum::DELETED);
            })
            ->limit($limit)
            ->get();
    }
}
