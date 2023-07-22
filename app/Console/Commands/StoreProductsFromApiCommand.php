<?php

namespace App\Console\Commands;

use App\Enums\ClientServiceStatusEnum;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Models\ClientService;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Console\Command;
use Throwable;

class StoreProductsFromApiCommand extends AbstractCommand
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
                for ($page = 1; $page < ResponseHelper::MAXIMUM_ITERATIONS; $page++) { 
                    try {
                        $productResponses = ConnectorHelper::getProducts($clientService, $page);
                        $products = Product::where('client_id', $currentClientId)->where('active', true)->get();
                        foreach ($productResponses as $productResponse) {
                            $this->info('Updating product ' . $productResponse->getGuid());
                            $productExists = false;
                            foreach ($products as $key => $product) {
                                if ($product->getAttribute('guid') === $productResponse->getGuid()) {
                                    unset($products[$key]);
                                    $productExists = true;
                                    break;
                                }
                            }
                            if ($productExists) {
                                continue;
                            }
                            $product = Product::where('client_id', $currentClientId)->where('guid', $productResponse->getGuid())->first();
                            if ($product === null) {
                                $product = new Product();
                                $product->setAttribute('guid', $productResponse->getGuid());
                                $product->setAttribute('client_id', $currentClientId);
                                $product->setAttribute('active', true);
                                $product->save();
                            } else if ($product->getAttribute('active') === false) {
                                $product->setAttribute('active', true);
                                $product->save();
                            }
                        }
                        foreach ($products as $product) {
                            $product->setAttribute('active', false);
                            $product->save();
                        }
                        if (count($productResponses) < ResponseHelper::MAXIMUM_ITEMS_PER_PAGE) {
                            break;
                        }
                    } catch (Throwable $t) {
                        $this->error('Error updating products ' . $t->getMessage());
                        LoggerHelper::log('Error updating products ' . $t->getMessage());
                        $success = false;
                        break;
                    }
                }
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
