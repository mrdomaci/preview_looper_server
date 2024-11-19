<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\DataNotFoundException;
use App\Models\ClientService;
use App\Models\ClientServiceQueue;
use DateTime;
use Throwable;

class ClientServiceQueueRepository
{
    public function createOrIgnore(ClientService $clientService): ?ClientServiceQueue
    {
        try {
            $clientServiceQueue = ClientServiceQueue::where('client_service_id', $clientService->getId())
                ->where('status', '!=', ClientServiceQueueStatusEnum::DONE->name)
                ->firstOrFail();
            return null;
        } catch (Throwable) {
            if ($clientService->getWebhoodAt() === null || $clientService->getWebhoodAt() < (new DateTime('-6 hours'))) {
                $clientServiceQueue = ClientServiceQueue::create([
                    'client_service_id' => $clientService->getId(),
                    'status' => ClientServiceQueueStatusEnum::CLIENTS->name,
                    'queued_at' => new DateTime(),
                ]);
                $clientService->setWebhoodAt(new DateTime());
                $clientService->save();
                return $clientServiceQueue;
            }
        }
        return null;
    }

    public function getNext(ClientServiceQueueStatusEnum $status): ?ClientServiceQueue
    {
        return ClientServiceQueue::where('status', $status->name)
            ->whereHas('clientService', function ($query) {
                $query->where('update_in_process', false)
                    ->where('status', ClientServiceStatusEnum::ACTIVE->name);
            })
            ->where(function ($query) {
                $query->where('queued_at', '<', now())
                      ->orWhereNull('queued_at');
            })
            ->orderBy('queued_at')
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

    public function prune(): void
    {
        ClientServiceQueue::where('status', ClientServiceQueueStatusEnum::DONE->name)
            ->delete();

        ClientServiceQueue::where('created_at', '<', now()->subDays(2))
            ->delete();

        ClientServiceQueue::join('client_services', 'client_services.id', '=', 'client_service_queues.client_service_id')
            ->where('client_services.status', ClientServiceStatusEnum::DELETED->name)
            ->delete();
    }
}
