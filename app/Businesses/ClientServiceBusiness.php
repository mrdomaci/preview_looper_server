<?php
declare(strict_types=1);

namespace App\Businesses;

use App\Enums\ClientServiceStatusEnum;
use App\Models\Client;
use App\Models\ClientService;
use DateTime;

class ClientServiceBusiness {
    public function hasActiveService(Client $client): bool {
        $isActive = false;
        $clientServices = $client->services();
        foreach ($clientServices->get(['id', 'status']) as $clientService) {
            if ($clientService->getAttribute('status') === ClientServiceStatusEnum::ACTIVE) {
                $isActive = true;
            }
        }
        return $isActive;
    }

    public function isForbidenToUpdate(ClientService $clientService): bool {
        if ($clientService->getAttribute('update_in_process') === true) {
            return true;
        }
        return false;
    }
}