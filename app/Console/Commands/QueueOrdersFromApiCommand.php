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
        private readonly QueueBusiness $queueBusiness,
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
        $clientServiceStatus = ClientServiceQueueStatusEnum::ORDERS;
        $clientServices = $this->clientServiceRepository->getForUpdate($clientServiceStatus, 5);
        if ($clientServices->isEmpty()) {
            $this->info('No client service in orders queue');
            return Command::SUCCESS;
        }
        $success = true;
        foreach ($clientServices as $clientService) {
            $clientService->setUpdateInProgress(true);
            $this->info('Client service ' . $clientService->getId() . ' orders update started');
    
            $orderFilters = [];
            if ($clientService->getOrdersLastSyncedAt() !== null) {
                $orderFilters[] = new OrderFilter('changeTimeFrom', $clientService->getOrdersLastSyncedAt());
            }
    
            try {
                $queueResponse = ConnectorHelper::queueOrders($clientService, $orderFilters);
                if ($queueResponse) {
                    $this->queueBusiness->createOrIgnoreFromResponse($clientService, $queueResponse, new Order());
                    $this->info('Client service ' . $clientService->getId() . ' orders queued');
                    $service = $clientService->service()->first();
                    $clientService->setQueueStatus($clientServiceStatus->next($service));
                    $clientService->save();
                }
            } catch (ApiRequestFailException $t) {
                LoggerHelper::log($t->getMessage());
                $clientService->setStatusInactive();
            } catch (ApiRequestTooManyRequestsException $t) {
                $this->error('Error updating orders due to too many requests ' . $t->getMessage());
                LoggerHelper::log('Error updating orders due to too many requests ' . $t->getMessage());
                $success = false;
            } catch (Throwable $t) {
                $this->error('Error updating orders ' . $t->getMessage());
                LoggerHelper::log('Error updating orders ' . $t->getMessage());
                $success = false;
            }
            $clientService->setUpdateInProgress(false);
        }
        if ($success) {
            return Command::SUCCESS;
        }
        return Command::FAILURE;
    }
}
