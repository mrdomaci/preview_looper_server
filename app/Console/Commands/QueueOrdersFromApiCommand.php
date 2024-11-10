<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Businesses\QueueBusiness;
use App\Connector\Shoptet\Order;
use App\Connector\Shoptet\OrderFilter;
use App\Enums\ClientServiceQueueStatusEnum;
use App\Exceptions\ApiRequestFailException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Repositories\ClientServiceQueueRepository;
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
        private readonly QueueBusiness $queueBusiness,
        private readonly ClientServiceQueueRepository $clientServiceQueueRepository,
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
        $clientServiceStatus = ClientServiceQueueStatusEnum::ORDERS;
        $clientServiceQueue = $this->clientServiceQueueRepository->getNext($clientServiceStatus);
        if ($clientServiceQueue === null) {
            $this->info('No client service in orders queue');
            return Command::SUCCESS;
        }
        $clientService = $clientServiceQueue->clientService()->first();
        $clientService->setUpdateInProgress(true);

        $orderFilters = [];
        if ($clientService->getOrdersLastSyncedAt() !== null) {
            $orderFilters[] = new OrderFilter('changeTimeFrom', $clientService->getOrdersLastSyncedAt());
        }

        try {
            $queueResponse = ConnectorHelper::queueOrders($clientService, $orderFilters);
            if ($queueResponse) {
                $this->queueBusiness->createOrIgnoreFromResponse($clientService, $queueResponse, new Order());
                $this->info('Client service ' . $clientService->getId() . ' orders queued');
                $clientServiceQueue->next();
            }
        } catch (ApiRequestFailException) {
            $clientService->setStatusInactive();
        } catch (ApiRequestTooManyRequestsException $t) {
            $this->error('Error updating orders due to too many requests ' . $t->getMessage());
            LoggerHelper::log('Error updating orders due to too many requests ' . $t->getMessage());
            return Command::FAILURE;
        } catch (Throwable $t) {
            $this->error('Error updating orders ' . $t->getMessage());
            LoggerHelper::log('Error updating orders ' . $t->getMessage());
            return Command::FAILURE;
        }
        $clientService->setUpdateInProgress(false);
        return Command::SUCCESS;
    }
}
