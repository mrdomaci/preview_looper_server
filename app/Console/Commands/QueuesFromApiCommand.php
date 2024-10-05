<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Businesses\ClientServiceBusiness;
use App\Businesses\QueueBusiness;
use App\Connector\Shoptet\QueueFilter;
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
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly ClientServiceBusiness $clientServiceBusiness,
        private readonly QueueBusiness $queueBusiness,
        private readonly QueueRepository $queueRepository,
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
        $success = true;
        $lastClientServiceId = 0;
        $yesterday = new DateTime('yesterday');
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = $this->clientServiceRepository->getActive(
                $lastClientServiceId,
                $this->findService(),
                $this->findClient(),
                $this->getIterationCount(),
            );
            foreach ($clientServices as $clientService) {
                $lastClientServiceId = $clientService->getId();
                if ($this->clientServiceBusiness->isForbidenToUpdate($clientService) === true) {
                    continue;
                }
                $clientService->setUpdateInProgress(true);
                $filterQueues = [];
                $filterQueues[] = new QueueFilter('status', 'completed');
                $filterQueues[] = new QueueFilter('creationTimeFrom', $yesterday);
                try {
                    $jobListResponse = ConnectorHelper::queues($clientService, $filterQueues);
                    if ($jobListResponse === null) {
                        break;
                    }
                    $this->queueBusiness->update($clientService, $jobListResponse);
                } catch (ApiRequestFailException) {
                    $clientService->setStatusInactive();
                    break;
                } catch (ApiRequestTooManyRequestsException) {
                    sleep(10);
                    continue;
                } catch (Throwable $t) {
                    $this->error('Error updating queues ' . $t->getMessage());
                    LoggerHelper::log('Error updating queues ' . $t->getMessage());
                    $success = false;
                    break;
                }
                
                $clientService->setUpdateInProgress(false);
            }

            if ($clientServices->count() < $this->getIterationCount()) {
                break;
            }
        }
        $this->queueRepository->deleteOld();
        $this->queueRepository->deleteExpired();
        if ($success === true) {
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
