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
use App\Repositories\ClientServiceQueueRepository;
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
        $clientServiceStatus = ClientServiceQueueStatusEnum::PRODUCTS;
        $clientServiceQueue = $this->clientServiceQueueRepository->getNext($clientServiceStatus);
        if ($clientServiceQueue === null) {
            $this->info('No client service in product queue');
            return Command::SUCCESS;
        }
        $clientService = $clientServiceQueue->clientService()->first();
        $clientService->setUpdateInProgress(true);

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
                $clientServiceQueue->next();
            }
        } catch (ApiRequestFailException) {
            $clientService->setStatusInactive();
            return Command::FAILURE;
        } catch (ApiRequestTooManyRequestsException $t) {
            $this->error('Error updating products due to too many requests ' . $t->getMessage());
            LoggerHelper::log('Error updating products due to too many requests ' . $t->getMessage());
            $clientService->setUpdateInProgress(false);
            return Command::FAILURE;
        } catch (Throwable $t) {
            $this->error('Error updating products ' . $t->getMessage());
            LoggerHelper::log('Error updating products ' . $t->getMessage());
            $clientService->setUpdateInProgress(false);
            return Command::FAILURE;
        } finally {
            $clientService->setUpdateInProgress(false);
        }
        $this->info('Client service ' . $clientService->getId() . ' queue products');
        return Command::SUCCESS;
    }
}
