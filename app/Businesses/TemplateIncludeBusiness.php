<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Helpers\ConnectorBodyHelper;
use App\Helpers\ConnectorHelper;
use App\Models\Client;
use App\Models\Service;

class TemplateIncludeBusiness
{
    public function post(Service $service, Client $client): void
    {
        $clientService = $client->services()->where('service_id', $service->getId())->first();
        $body = ConnectorBodyHelper::getStringBodyForTemplateInclude($service, $client);
        ConnectorHelper::postTemplateInclude($clientService, $body);
    }
}
