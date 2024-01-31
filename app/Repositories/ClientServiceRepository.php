<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ClientServiceStatusEnum;
use App\Models\ClientService;
use App\Models\Service;
use DateTime;
use Illuminate\Database\Eloquent\Collection;

class ClientServiceRepository {
    /**
     * @param int $lastId
     * @param Service|null $service
     * @param int|null $clientId
     * @param int $iterationCount
     * @return Collection<ClientService>
     */
    public function getActive(int $lastId, ?Service $service = null, ?int $clientId = null, ?int $iterationCount = 100): Collection {
        $query = ClientService::where('status', ClientServiceStatusEnum::ACTIVE)
            ->where('id', '>', $lastId);
        if ($service !== null) {
            $query->where('service_id', $service->getAttribute('id'));
        }

        if ($clientId !== null) {
            $query->where('client_id', $clientId);
        }

        return $query->limit($iterationCount)
            ->get();
    }

    public function getNextForUpdate(Service $service, DateTime $dateLastSync): ?ClientService {
        $q = ClientService::where('service_id', $service->getAttribute('id'))
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

        return $q->first();
    }
}