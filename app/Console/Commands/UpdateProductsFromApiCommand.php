<?php

namespace App\Console\Commands;

use App\Connector\ProductFilter;
use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\ApiRequestFailException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\ConnectorHelper;
use App\Helpers\GeneratorHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Models\ClientService;
use App\Models\Product;
use App\Models\Service;
use DateTime;
use Illuminate\Console\Command;
use Throwable;

class UpdateProductsFromApiCommand extends AbstractCommand
{
/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:changed:products {client_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store changed products from API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {        
        $clientId = $this->argument('client_id');
        $success = true;
        $service = Service::find(Service::DYNAMIC_PREVIEW_IMAGES);

        for($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            if ($clientId !== null) {
                $clientServices = ClientService::where('service_id', $service->getAttribute('id'))
                    ->where('status', ClientServiceStatusEnum::ACTIVE)
                    ->where('client_id', $clientId)
                    ->limit($this->getIterationCount())
                    ->offset($this->getOffset($i))
                    ->get();
            } else {
                $clientServices = ClientService::where('service_id', $service->getAttribute('id'))
                    ->where('status', ClientServiceStatusEnum::ACTIVE)
                    ->limit($this->getIterationCount())
                    ->offset($this->getOffset($i))
                    ->get();
            }

            foreach ($clientServices as $clientService) {
                $currentClientId = $clientService->getAttribute('client_id');
                if ($clientService->getAttribute('date_last_synced') !== null &&
                    $clientService->getAttribute('date_last_synced') >= now()->subHours(12)) {
                    continue;
                }
                if ($clientService->getAttribute('update_in_process') === true) {
                    continue;
                }
                $clientService->setUpdateInProgress(true);
                $clientService->save();
                $dateLastSynced = new DateTime($clientService->getAttribute('date_last_synced'));
                $products = Product::where('client_id', $currentClientId)->where('active', true)->get(['id', 'guid', 'active']);
                $productFilter = new ProductFilter('changeTimeFrom', $dateLastSynced->format('Y-m-d') . 'T' . $dateLastSynced->format('H:i:s') . '+0100');
                for ($page = 1; $page < ResponseHelper::MAXIMUM_ITERATIONS; $page++) { 
                    try {
                        $productListResponse = ConnectorHelper::getProducts($clientService, $page, $productFilter);
                        if ($productListResponse === null) {
                            break;
                        }
                        foreach (GeneratorHelper::fetchProducts($clientService, $productFilter, $page) as $productResponse) {
                            $this->info('Updating product ' . $productResponse->getGuid());
                            foreach ($products as $key => $product) {
                                if ($product->getAttribute('guid') === $productResponse->getGuid()) {
                                    unset($products[$key]);
                                    break;
                                }
                            }
                            $product = Product::where('client_id', $currentClientId)->where('guid', $productResponse->getGuid())->first();
                            if ($product === null) {
                                $product = new Product();
                                $product->setAttribute('guid', $productResponse->getGuid());
                                $product->setAttribute('client_id', $currentClientId);
                            }
                            $product->setAttribute('active', true);
                            $product->setAttribute('name', $productResponse->getName());
                            $product->setAttribute('url', $productResponse->getUrl());
                            $product->setAttribute('producer', $productResponse->getBrand()?->getName());
                            $product->setAttribute('category', $productResponse->getDefaultCategory()?->getName());
                            $product->save();
                        }
                        if ($productListResponse->getPage() === $productListResponse->getPageCount()) {
                            break;
                        }
                    } catch (ApiRequestFailException) {
                        $clientService->setAttribute('status', ClientServiceStatusEnum::INACTIVE);
                        $clientService->setUpdateInProgress(false);
                        $clientService->save();
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
                foreach ($products as $product) {
                    $product->setAttribute('active', false);
                    $product->save();
                }
                $clientService->setUpdateInProgress(false);
                $clientService->save();
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
