<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Connector\Shoptet\JobResponse;
use App\Connector\Shoptet\QueueResponse;
use App\Enums\QueueStatusEnum;
use App\Exceptions\DataNotFoundException;
use App\Models\ClientService;
use App\Models\Queue;
use Exception;

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

    public function createOrIgnoreFromResponse(
        ClientService $clientService,
        QueueResponse $response,
    ): void {
        $queue = Queue::where('client_service_id', $clientService->getId())
            ->where('job_id', $response->getJobId())
            ->first();
        if ($queue === null) {
            $queue = new Queue();
            $queue->setClientServiceId($clientService->id);
            $queue->setJobId($response->getJobId());
            $queue->status = QueueStatusEnum::PENDING->name;
            $queue->save();
        }
    }

    public function updateOrCreate(
        ClientService $clientService,
        JobResponse $response,
    ): void
    {
        $queue = Queue::where('client_service_id', $clientService->getId())
            ->where('job_id', $response->getJobId())
            ->first();
        if ($queue === null) {
            $queue = new Queue();
            $queue->setClientServiceId($clientService->id);
            $queue->setJobId($response->getJobId());
        }
        if ($queue->status === $response->getStatus()->name) {
            return;
        }
        $queue->status = $response->getStatus()->name;
        $queue->reqsult_url = $response->getResultUrl();
        $queue->endpoint = $response->getEndpoint();
        $queue->save();
    }
}
