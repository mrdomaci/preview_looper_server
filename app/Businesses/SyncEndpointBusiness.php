<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Helpers\WebHookHelper;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Service;

class SyncEndpointBusiness
{
    public function syncClientService(Client $client, Service $service): void
    {
        /**
         * @var ClientService $clientService
         */
        $clientService = $client->services()->where('service_id', $service->getId())->first();
        $clientService->setUpdateInProgress(false);
        WebHookHelper::webhookResolver($clientService);
    }
}
