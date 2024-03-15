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
use Throwable;

class ClientRepository
{

    public function get(int $id): Client
    {
        $client = Client::find($id);
        if ($client === null) {
            throw new DataNotFoundException(new Exception('Client not found id: ' . $id));
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
            ->setLastSyncedAt(now()->toDateTime())
            ->save();
    }

    public function getByEshopId(int $eshopID): Client
    {
        return Client::where('eshop_id', $eshopID)->firstOrFail();
    }

    public function updateOrCreate(int $eshopId, string $eshopUrl, string $email): Client
    {
        try {
            $client = $this->getByEshopId($eshopId);
            $client->url = $eshopUrl;
            $client->email = $email;
        } catch (Throwable) {
            $client = Client::create([
                'eshop_id' => $eshopId,
                'url' => $eshopUrl,
                'email' => $email,
            ]);
        }
        $client->save();

        return $client;
    }
}
