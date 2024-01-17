<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ClientServiceStatusEnum;
use App\Models\ClientService;
use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;

class ClientServiceRepository {
    public function getActiveClientServices(Service $service, int $clientId = null, int $iterationCount = 100, int $offset = 0): Collection {
        $query = ClientService::where('service_id', $service->getAttribute('id'))
            ->where('status', ClientServiceStatusEnum::ACTIVE);

        if ($clientId !== null) {
            $query->where('client_id', $clientId);
        }

        return $query->limit($iterationCount)
            ->offset($offset)
            ->get();
    }
}