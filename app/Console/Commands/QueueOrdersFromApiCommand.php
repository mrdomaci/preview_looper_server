<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Businesses\ClientServiceBusiness;
use App\Businesses\QueueBusiness;
use App\Connector\Shoptet\Order;
use App\Connector\Shoptet\OrderFilter;
use App\Enums\SyncEnum;
use App\Exceptions\ApiRequestFailException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Repositories\ClientServiceRepository;
use Illuminate\Console\Command;
use Throwable;

class QueueOrdersFromApiCommand extends AbstractClientServiceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:orders {--client=} {--service=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue orders from API';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly ClientServiceBusiness $clientServiceBusiness,
        private readonly QueueBusiness $queueBusiness,
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

                $orderFilters = [];
                if ($clientService->getOrdersLastSyncedAt() !== null) {
                    $orderFilters[] = new OrderFilter('changeTimeFrom', $clientService->getOrdersLastSyncedAt());
                }

                try {
                    $queueResponse = ConnectorHelper::queueOrders($clientService, $orderFilters);
                    if ($queueResponse) {
                        $this->queueBusiness->createOrIgnoreFromResponse($clientService, $queueResponse, new Order());
                    }
                    // TODO: set client service queue status to queue
                } catch (ApiRequestFailException) {
                    $clientService->setStatusInactive();
                } catch (ApiRequestTooManyRequestsException) {
                    sleep(10);
                    continue;
                } catch (Throwable $t) {
                    $this->error('Error updating orders ' . $t->getMessage());
                    LoggerHelper::log('Error updating orders ' . $t->getMessage());
                    $success = false;
                }
                
                $clientService->setUpdateInProgress(false, SyncEnum::ORDER);
            }

            if ($clientServices->count() < $this->getIterationCount()) {
                break;
            }
        }
        if ($success === true) {
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
