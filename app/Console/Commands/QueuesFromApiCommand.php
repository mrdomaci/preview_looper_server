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
use App\Repositories\ClientServiceQueueRepository;
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
        private readonly ClientServiceQueueRepository $clientServiceQueueRepository,
        private readonly QueueRepository $queueRepository
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
        $clientServiceQueue = $this->clientServiceQueueRepository->getNext($clientServiceStatus);
        if ($clientServiceQueue === null) {
            $this->info('No client service in api queue');
            return Command::SUCCESS;
        }
        $clientService = $clientServiceQueue->clientService()->first();
        $clientService->setUpdateInProgress(true);
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
            if ($this->queueRepository->isFinished($clientService)) {
                $clientServiceQueue->next();
            } else {
                $clientServiceQueue->queued_at = now()->addMinutes(90);
                $clientServiceQueue->save();
            }
        } catch (ApiRequestFailException) {
            $clientService->setStatusInactive();
        } catch (ApiRequestTooManyRequestsException $t) {
            $this->error('Error updating orders due to too many requests ' . $t->getMessage());
            LoggerHelper::log('Error updating orders due to too many requests ' . $t->getMessage());
            return Command::FAILURE;
        } catch (Throwable $t) {
            $this->error('Error updating queues ' . $t->getMessage());
            LoggerHelper::log('Error updating queues ' . $t->getMessage());
            return Command::FAILURE;
        }
        $clientService->setUpdateInProgress(false);
        $this->info('Client service ' . $clientService->getId() . ' queues updated');
        return Command::SUCCESS;
    }
}
