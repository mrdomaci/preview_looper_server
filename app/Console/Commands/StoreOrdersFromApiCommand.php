<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Businesses\ClientServiceBusiness;
use App\Connector\OrderResponse;
use App\Enums\SyncEnum;
use App\Exceptions\ApiRequestFailException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\ConnectorHelper;
use App\Helpers\GeneratorHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Models\ClientService;
use App\Models\Service;
use App\Repositories\ClientServiceRepository;
use App\Repositories\OrderProductRepository;
use App\Repositories\OrderRepository;
use Illuminate\Console\Command;
use Throwable;

class StoreOrdersFromApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:orders {client_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store orders from API';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly ClientServiceBusiness $clientServiceBusiness,
        private readonly OrderRepository $orderRepository,
        private readonly OrderProductRepository $orderProductRepository,
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
        $clientId = $this->argument('client_id');
        if ($clientId !== null) {
            $clientId = (int) $clientId;
        }
        $success = true;
        $this->info('Updating orders');
        $lastClientServiceId = 0;
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = $this->clientServiceRepository->getActive(
                $lastClientServiceId,
                Service::getUpsell(),
                $clientId,
                $this->getIterationCount(),
            );

            /** @var ClientService $clientService */
            foreach ($clientServices as $clientService) {
                $lastClientServiceId = $clientService->getId();
                if ($this->clientServiceBusiness->isForbidenToUpdate($clientService)) {
                    continue;
                }

                $clientService->setUpdateInProgress(true);
                $client = $clientService->client()->first();

                $ordersLastSynced = $clientService->getOrdersLastSyncedAt();

                for ($page = 1; $page < ResponseHelper::MAXIMUM_ITERATIONS; $page++) {
                    try {
                        $orderListResponse = ConnectorHelper::getOrders($clientService, $page, $ordersLastSynced);
                        if ($orderListResponse === null) {
                            break;
                        }
                        /** @var OrderResponse $orderResponse */
                        foreach (GeneratorHelper::fetchOrders($clientService, $page, $ordersLastSynced) as $orderResponse) {
                            $this->info('Updating order ' . $orderResponse->getGuid());

                            $order = $this->orderRepository->createOrUpdate($orderResponse, $client);
                            foreach (GeneratorHelper::fetchOrderDetail($clientService, $orderResponse->getCode()) as $orderDetailResponse) {
                                $this->orderProductRepository->createOrUpdate($orderResponse, $orderDetailResponse, $client, $order);
                            }
                        }
                        if ($orderListResponse->getPage() === $orderListResponse->getPageCount()) {
                            break;
                        }
                    } catch (ApiRequestFailException) {
                        $clientService->setStatusInactive();
                        break;
                    } catch (ApiRequestTooManyRequestsException) {
                        sleep(10);
                        $page--;
                        continue;
                    } catch (Throwable $t) {
                        $this->error('Error updating orders ' . $t->getMessage());
                        LoggerHelper::log('Error updating orders ' . $t->getMessage());
                        $success = false;
                        break;
                    }
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
