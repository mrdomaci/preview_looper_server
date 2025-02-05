<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Businesses\QueueBusiness;
use App\Connector\Shoptet\QueueFilter;
use App\Enums\ClientServiceQueueStatusEnum;
use App\Exceptions\ApiRequestFailException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Repositories\ClientServiceRepository;
use App\Repositories\QueueRepository;
use DateTime;
use Illuminate\Console\Command;
use Throwable;

class QueuesFromApiCommand extends AbstractClientServiceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:check {--client=} {--service=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check queue status from API';

    public function __construct(
        private readonly QueueBusiness $queueBusiness,
        private readonly QueueRepository $queueRepository,
        private readonly ClientServiceRepository $clientServiceRepository,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clientServiceStatus = ClientServiceQueueStatusEnum::API;
        $clientServices = $this->clientServiceRepository->getForUpdate($clientServiceStatus, 5);
        if ($clientServices->isEmpty()) {
            $this->info('No client service in api queue');
            return Command::SUCCESS;
        }
        $success = true;
        foreach ($clientServices as $clientService) {
            $clientService->setUpdateInProgress(true);
            $this->info('Client service ' . $clientService->getId() . ' queues update started');
            $yesterday = new DateTime('yesterday');

            $filterQueues = [];
            $filterQueues[] = new QueueFilter('status', 'completed');
            $filterQueues[] = new QueueFilter('creationTimeFrom', $yesterday);

            try {
                $jobListResponse = ConnectorHelper::queues($clientService, $filterQueues);
                if ($jobListResponse) {
                    $this->queueBusiness->update($clientService, $jobListResponse);
                    $this->info('Queues updated');
                }
                $queues = $this->queueRepository->getCompletedForClientService($clientService);
                foreach ($queues as $queue) {
                    $this->queueBusiness->download($queue);
                }
                $this->info($this->queueRepository->isFinished($clientService) ? 'All queues finished' : 'Queues not finished');
                if ($this->queueRepository->isFinished($clientService)) {
                    $service = $clientService->service()->first();
                    $clientService->setQueueStatus($clientServiceStatus->next($service));
                    $clientService->save();
                } else {
                    $clientService->setWebhookedAt(new DateTime('now + 30 minutes'));
                    $clientService->save();
                }
            } catch (ApiRequestFailException) {
                $clientService->setStatusInactive();
            } catch (ApiRequestTooManyRequestsException $t) {
                $this->error('Error updating orders due to too many requests ' . $t->getMessage());
                LoggerHelper::log('Error updating orders due to too many requests ' . $t->getMessage());
                $success = false;
            } catch (Throwable $t) {
                $this->error('Error updating queues ' . $t->getMessage());
                LoggerHelper::log('Error updating queues ' . $t->getMessage());
                $success = false;
            }
            $clientService->setUpdateInProgress(false);
            $this->info('Client service ' . $clientService->getId() . ' queues updated');
        }
        if ($success) {
            return Command::SUCCESS;
        }
        return Command::FAILURE;
    }
}
