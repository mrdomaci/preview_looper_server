<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Connector\Shoptet\Endpoint;
use App\Connector\Shoptet\JobResponse;
use App\Connector\Shoptet\QueueResponse;
use App\Enums\QueueStatusEnum;
use App\Exceptions\DataNotFoundException;
use App\Models\ClientService;
use App\Models\Queue;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class QueueRepository
{

    public function get(int $id): Queue
    {
        $entity = Queue::find($id);
        if ($entity === null) {
            throw new DataNotFoundException(new Exception('Queue not found id: ' . $id));
        }
        return $entity;
    }

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
        $results = Queue::where('client_service_id', $clientService->getId())
            ->where('created_at', '>', now()->subHours(5))
            ->whereNull('result_url')
            ->where('status', '!=', QueueStatusEnum::EXPIRED)
            ->get();
        return $results->isEmpty();
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
                ->setStatus(QueueStatusEnum::PENDING);
            $queue->save();
        }
    }

    public function updateOrCreate(
        ClientService $clientService,
        JobResponse $response,
    ): void {
        $queue = Queue::where('client_service_id', $clientService->getId())
            ->where('job_id', $response->getJobId())
            ->first();
        if ($queue === null) {
            $queue = new Queue();
            $queue->setClientServiceId($clientService->id)
                ->setJobId($response->getJobId());
        }
        $queue->setStatus($response->getStatus())
            ->setResultUrl($response->getResultUrl())
            ->setEndpoint($response->getEndpoint())
            ->setType($response->getType());
        $queue->save();
    }

    public function deleteForClientService(ClientService $clientService): void
    {
        Queue::where('client_service_id', $clientService->getId())->delete();
    }
}
