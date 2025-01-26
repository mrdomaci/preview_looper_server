<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Connector\Shoptet\Endpoint;
use App\Connector\Shoptet\JobListResponse;
use App\Connector\Shoptet\Order;
use App\Connector\Shoptet\QueueResponse;
use App\Enums\QueueStatusEnum;
use App\Exceptions\AddonNotInstalledException;
use App\Exceptions\AddonSuspendedException;
use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\Helpers\ConnectorHelper;
use App\Helpers\StringHelper;
use App\Models\ClientService;
use App\Models\Queue;
use App\Repositories\QueueRepository;
use Exception;
use Throwable;

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

    public function download(Queue $queue): void
    {
        $clientService = $queue->clientService()->first();
        try {
            $response = ConnectorHelper::queue($clientService, $queue);
        } catch (AddonNotInstalledException) {
            $queue->delete();
            $clientService->setStatusDeleted();
            return;
        } catch (AddonSuspendedException $e) {
            $queue->delete();
            $clientService->setStatusInactive();
            return;
        } catch (Throwable $e) {
            return;
        }
        if (StringHelper::contains($queue->getEndpoint(), (new Order())->getEndpoint())) {
            $domain = 'orders';
        } else {
            $domain = 'products';
        }
        $localFilePath = storage_path('app/snapshots/' . $clientService->getId() . '_' . $domain . '.gz');
        if ($response->getResultUrl() === null) {
            $queue->delete();
            return;
        }
        $fileContent = file_get_contents($response->getResultUrl());
        if ($fileContent === false) {
            throw new FileNotFoundException(new Exception('File not found: ' . $response->getResultUrl()));
        } else {
            $fileSaved = file_put_contents($localFilePath, $fileContent);
            if ($fileSaved === false) {
                throw new FileErrorException(new Exception('File error: ' . $localFilePath));
            }
        }
        $queue->setStatus(QueueStatusEnum::DONE->value);
        $queue->setResultUrl($response->getResultUrl());
        $queue->save();
    }
}
