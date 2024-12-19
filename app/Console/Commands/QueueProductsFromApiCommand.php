<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Businesses\QueueBusiness;
use App\Connector\Shoptet\Product;
use App\Connector\Shoptet\ProductFilter;
use App\Enums\ClientServiceQueueStatusEnum;
use App\Exceptions\ApiRequestFailException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\StringHelper;
use App\Repositories\ClientServiceRepository;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class QueueProductsFromApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue products from API';

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
        $clientServiceStatus = ClientServiceQueueStatusEnum::PRODUCTS;
        $clientServices = $this->clientServiceRepository->getForUpdate($clientServiceStatus, 5);
        if ($clientServices->isEmpty()) {
            $this->info('No client service in product queue');
            return Command::SUCCESS;
        }
        $success = true;
        foreach ($clientServices as $clientService) {
            $clientService->setUpdateInProgress(true);
            $this->info('Client service ' . $clientService->getId() . ' products update started');

            $productFilters = [];
            $productFilters[] = new ProductFilter('visibility', 'visible');
            $productFilters[] = new ProductFilter('include', 'images,allCategories');
            try {
                ConnectorHelper::postWebhook($clientService);
            } catch (Throwable $t) {
                if (StringHelper::contains($t->getMessage(), 'webhook-event-already-registered') === false) {
                    throw new ApiRequestFailException(new Exception($t->getMessage(), $t->getCode(), $t));
                }
            }

            try {
                $queueResponse = ConnectorHelper::queueProducts($clientService, $productFilters);
                if ($queueResponse) {
                    $this->queueBusiness->createOrIgnoreFromResponse($clientService, $queueResponse, new Product());
                    $this->info('Client service ' . $clientService->getId() . ' products queued');
                    $service = $clientService->service()->first();
                    $clientService->setQueueStatus($clientServiceStatus->next($service));
                    $clientService->save();
                }
            } catch (ApiRequestFailException $t) {
                LoggerHelper::log($t->getMessage());
                $clientService->setStatusInactive();
            } catch (ApiRequestTooManyRequestsException $t) {
                $this->error('Error updating products due to too many requests ' . $t->getMessage());
                LoggerHelper::log('Error updating products due to too many requests ' . $t->getMessage());
                $success = false;
            } catch (Throwable $t) {
                $this->error('Error updating products ' . $t->getMessage());
                LoggerHelper::log('Error updating products ' . $t->getMessage());
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
