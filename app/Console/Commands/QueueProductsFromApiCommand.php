<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Businesses\ClientServiceBusiness;
use App\Businesses\QueueBusiness;
use App\Connector\Shoptet\Product;
use App\Connector\Shoptet\ProductFilter;
use App\Enums\SyncEnum;
use App\Exceptions\ApiRequestFailException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\StringHelper;
use App\Repositories\ClientServiceRepository;
use Exception;
use Illuminate\Console\Command;
use Throwable;

class QueueProductsFromApiCommand extends AbstractClientServiceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:products {--client=} {--service=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queue products from API';

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
                    }
                    // TODO: set client service queue status to orders
                } catch (ApiRequestFailException) {
                    $clientService->setStatusInactive();
                } catch (ApiRequestTooManyRequestsException) {
                    sleep(10);
                    continue;
                } catch (Throwable $t) {
                    $this->error('Error updating products ' . $t->getMessage());
                    LoggerHelper::log('Error updating products ' . $t->getMessage());
                    $success = false;
                }
                
                $clientService->setUpdateInProgress(false, SyncEnum::PRODUCT);
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
