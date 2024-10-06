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
        return Queue::where('status', QueueStatusEnum::COMPLETED->name)
                ->whereIsNull('reqsult_url')
                ->limit($limit)->get();
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
            $queue->setClientServiceId($clientService->id);
            $queue->setJobId($response->getJobId());
            $queue->setEndpoint($endpoint->getEndpoint());
            $queue->status = QueueStatusEnum::PENDING->name;
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
            $queue->setClientServiceId($clientService->id);
            $queue->setJobId($response->getJobId());
        }
        $queue->status = $response->getStatus()->name;
        $queue->result_url = $response->getResultUrl();
        $queue->endpoint = $response->getEndpoint();
        $queue->save();
    }

    public function deleteOld(): void
    {
        Queue::where('created_at', '<', now()->subDays(1))->delete();
    }

    public function deleteExpired(): void
    {
        Queue::where('status', 'EXPIRED')->delete();
    }
}
