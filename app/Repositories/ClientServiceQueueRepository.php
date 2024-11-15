<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Exceptions\DataNotFoundException;
use App\Models\ClientService;
use App\Models\ClientServiceQueue;
use Throwable;

class ClientServiceQueueRepository
{
    public function createOrIgnore(ClientService $clientService): ClientServiceQueue
    {
        try {
            $clientServiceQueue = ClientServiceQueue::where('client_service_id', $clientService->getId())
                ->where('status', '!=', ClientServiceQueueStatusEnum::DONE->name)
                ->firstOrFail();
        } catch (Throwable) {
            $clientServiceQueue = ClientServiceQueue::create([
                'client_service_id' => $clientService->getId(),
                'status' => ClientServiceQueueStatusEnum::CLIENTS->name,
            ]);
        }
        return $clientServiceQueue;
    }

    public function getNext(ClientServiceQueueStatusEnum $status): ?ClientServiceQueue
    {
        return ClientServiceQueue::where('status', $status->name)
            ->whereHas('clientService', function ($query) {
                $query->where('update_in_process', false);
            })
            ->where('created_at', '<', now())
            ->orderBy('created_at')
            ->first();
    }

    public function get(int $id): ClientServiceQueue
    {
        $entity = ClientServiceQueue::find($id);
        if ($entity === null) {
            throw new DataNotFoundException(new \Exception('ClientServiceQueue not found id: ' . $id));
        }
        return $entity;
    }
}
