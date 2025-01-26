<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Connector\Shoptet\Endpoint;
use App\Connector\Shoptet\JobResponse;
use App\Connector\Shoptet\QueueResponse;
use App\Enums\QueueStatusEnum;
use App\Enums\QueueTypeEnum;
use App\Exceptions\DataNotFoundException;
use App\Models\ClientService;
use App\Models\Queue;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class QueueRepository
{
    public function getByJobId(string $jobId): Queue
    {
        $entity = Queue::where('job_id', $jobId)->first();
        if ($entity === null) {
            throw new DataNotFoundException(new Exception('Queue not found job_id: ' . $jobId));
        }
        return $entity;
    }

    public function getCompleted(int $limit): Collection
    {
        return Queue::where('status', QueueStatusEnum::COMPLETED)
                ->whereNull('result_url')
                ->limit($limit)->get();
    }

    public function isFinished(ClientService $clientService): bool
    {
        $service = $clientService->service()->first();
        $queue = Queue::where('client_service_id', $clientService->getId())
            ->where('status', QueueStatusEnum::DONE)
            ->where('type', QueueTypeEnum::PRODUCT)
            ->get();

        if ($queue->isEmpty()) {
            return false;
        }

        if ($service->isDynamicPreviewImages()) {
            return true;
        }

        $queue = Queue::where('client_service_id', $clientService->getId())
            ->where('status', QueueStatusEnum::DONE)
            ->where('type', QueueTypeEnum::ORDER)
            ->get();

        if ($queue->isEmpty()) {
            return false;
        }
        return true;
    }

    public function createOrIgnoreFromResponse(
        ClientService $clientService,
        QueueResponse $response,
        ?Endpoint $endpoint,
    ): void {
        $queue = Queue::where('client_service_id', $clientService->getId())
            ->where('job_id', $response->getJobId())
            ->first();
        if ($queue === null) {
            $queue = new Queue();
            $queue->setClientServiceId($clientService->id)
                ->setJobId($response->getJobId())
                ->setEndpoint($endpoint->getEndpoint())
                ->setStatus(QueueStatusEnum::PENDING->value);
            $queue->save();
        }
    }

    public function updateOrCreate(
        ClientService $clientService,
        JobResponse $response,
    ): void {
        Queue::updateOrCreate(
            [
                'client_service_id' => $clientService->getId(),
                'job_id' => $response->getJobId(),
                'type' => $response->getType()->value,
            ],
            [
                'status' => $response->getStatus()->value,
                'endpoint' => $response->getEndpoint(),
            ]
        );
    }

    public function deleteForClientService(ClientService $clientService): void
    {
        Queue::where('client_service_id', $clientService->getId())->delete();
    }

    /**
     * @param ClientService $clientService
     * @return Collection<Queue>
     */
    public function getCompletedForClientService(ClientService $clientService): Collection
    {
        return Queue::where('client_service_id', $clientService->getId())
            ->where('status', QueueStatusEnum::COMPLETED)
            ->get();
    }
}
