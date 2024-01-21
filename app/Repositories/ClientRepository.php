<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Connector\EshopResponse;
use App\Enums\ClientServiceStatusEnum;
use App\Models\Client;
use App\Models\ClientService;
use Illuminate\Database\Eloquent\Collection;

class ClientRepository {
    /**
     * @param int $lastClientId
     * @param int|null $clientId
     * @return Collection<Client>
     */
    public function getClients(int $lastClientId, ?int $clientId): Collection {
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
    public function updateFromResponse(ClientService $clientService, EshopResponse $response): void {
        $clientService->setAttribute('status', ClientServiceStatusEnum::ACTIVE);
        $clientService->save();

        $client = $clientService->client()->first();

        $client->setAttribute('eshop_name', $response->getName());
        $client->setAttribute('url', $response->getUrl());
        $client->setAttribute('eshop_category', $response->getCategory());
        $client->setAttribute('eshop_subtitle', $response->getSubtitle());
        $client->setAttribute('contact_person', $response->getContactPerson());
        $client->setAttribute('email', $response->getEmail());
        $client->setAttribute('phone', $response->getPhone());
        $client->setAttribute('street', $response->getStreet());
        $client->setAttribute('city', $response->getCity());
        $client->setAttribute('zip', $response->getZip());
        $client->setAttribute('country', $response->getCountry());
        $client->setAttribute('last_synced_at', now());
        $client->save();
    }
}