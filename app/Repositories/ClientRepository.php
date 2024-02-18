<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Connector\EshopResponse;
use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\DataNotFoundException;
use App\Models\Client;
use App\Models\ClientService;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class ClientRepository
{

    public function get(int $id): Client
    {
        $client = Client::find($id);
        if ($client === null) {
            throw new DataNotFoundException(new Exception('Client not found'));
        }
        return $client;
    }
    /**
     * @param int $lastClientId
     * @param int|null $clientId
     * @return Collection<Client>
     */
    public function getClients(int $lastClientId, ?int $clientId): Collection
    {
        $query = Client::limit(10)->where('id', '>=', $lastClientId);
        if ($clientId !== null) {
            $query->where('id', $clientId);
        }
        return $query->get();
    }

    /**
     * @param ClientService $clientService
     * @param EshopResponse $response
     */
    public function updateFromResponse(ClientService $clientService, EshopResponse $response): void
    {
        $clientService->setAttribute('status', ClientServiceStatusEnum::ACTIVE);
        $clientService->save();

        /** @var Client $client */
        $client = $clientService->client()->first();

        $client->setEshopName($response->getName())
            ->setUrl($response->getUrl())
            ->setEshopCategory($response->getCategory())
            ->setEshopSubtitle($response->getSubtitle())
            ->setContactPerson($response->getContactPerson())
            ->setEmail($response->getEmail())
            ->setPhone($response->getPhone())
            ->setStreet($response->getStreet())
            ->setCity($response->getCity())
            ->setZip($response->getZip())
            ->setCountry($response->getCountry())
            ->setLastSyncedAt(now())
            ->save();
    }

    public function findByEshopId(int $eshopID): ?Client
    {
        return Client::where('eshop_id', $eshopID)->first();
    }

    public function getByEshopId(int $eshopID): Client
    {
        $client = $this->findByEshopId($eshopID);
        if ($client === null) {
            throw new DataNotFoundException(new Exception('Client not found'));
        }
        return Client::where('eshop_id', $eshopID)->first();
    }
}
