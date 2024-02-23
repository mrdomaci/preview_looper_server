<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Businesses\ClientServiceBusiness;
use App\Connector\ProductFilter;
use App\Exceptions\ApiRequestFailException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\ConnectorHelper;
use App\Helpers\GeneratorHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Repositories\ClientServiceRepository;
use App\Repositories\ProductRepository;
use Illuminate\Console\Command;
use Throwable;

class StoreProductsFromApiCommand extends AbstractClientCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:products {client_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store products from API';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly ClientServiceBusiness $clientServiceBusiness,
        private readonly ProductRepository $productRepository,
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
        $clientId = $this->getClientId();
        $success = true;
        $lastClientServiceId = 0;
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = $this->clientServiceRepository->getActive(
                $lastClientServiceId,
                null,
                $clientId,
                $this->getIterationCount(),
            );

            foreach ($clientServices as $clientService) {
                $lastClientServiceId = $clientService->getId();
                if ($this->clientServiceBusiness->isForbidenToUpdate($clientService) === true) {
                    continue;
                }
                $clientService->setUpdateInProgress(true);
                $client = $clientService->client()->first();

                $productFilter = new ProductFilter('visibility', 'visible');
                for ($page = 1; $page < ResponseHelper::MAXIMUM_ITERATIONS; $page++) {
                    try {
                        $productListResponse = ConnectorHelper::getProducts($clientService, $page, $productFilter);
                        if ($productListResponse === null) {
                            break;
                        }
                        foreach (GeneratorHelper::fetchProducts($clientService, $productFilter, $page) as $productResponse) {
                            $this->info('Updating product ' . $productResponse->getGuid());
                            $this->productRepository->createOrUpdateFromResponse($client, $productResponse);
                        }
                        if ($productListResponse->getPage() === $productListResponse->getPageCount()) {
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
                        $this->error('Error updating products ' . $t->getMessage());
                        LoggerHelper::log('Error updating products ' . $t->getMessage());
                        $success = false;
                        break;
                    }
                }
                $clientService->setUpdateInProgress(false);
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
