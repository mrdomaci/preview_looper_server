<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Connector\Shoptet\Endpoint;
use App\Connector\Shoptet\JobListResponse;
use App\Connector\Shoptet\QueueResponse;
use App\Models\ClientService;
use App\Repositories\QueueRepository;

class QueueBusiness
{
    public function __construct(
        private QueueRepository $queueRepository
    ) {
    }
    public function createOrIgnoreFromResponse(ClientService $clientService, ?QueueResponse $queueResponse, ?Endpoint $endpoint): void
    {
        if ($queueResponse === null) {
            return;
        }
        $this->queueRepository->createOrIgnoreFromResponse(
            $clientService,
            $queueResponse,
            $endpoint,
        );
    }

    public function update(ClientService $clientService, JobListResponse $jobListResponse): void
    {
        foreach ($jobListResponse->getJobs() as $job) {
            $this->queueRepository->updateOrCreate($clientService, $job);
        }
    }
}
