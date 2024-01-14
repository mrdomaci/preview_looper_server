<?php

namespace App\Console\Commands;

use App\Connector\OrderStatusResponse;
use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\ApiRequestFailException;
use App\Exceptions\ApiRequestTooManyRequestsException;
use App\Helpers\GeneratorHelper;
use App\Helpers\LoggerHelper;
use App\Models\Category;
use App\Models\ClientService;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Console\Command;
use Throwable;

class StoreProductCategoriesCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:product-categories {client_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store product categories from products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {        
        $clientId = $this->argument('client_id');
        $service = Service::find(Service::UPSELL);

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
                $clientService->setUpdateInProgress(true);
                $clientService->save();

                $lastProductId = 0;
                for($j = 0; $j < $this->getMaxIterationCount(); $j++) {
                    $products = Product::where('client_id', $currentClientId)
                        ->where('active', true)
                        ->where('id', '>', $lastProductId)
                        ->get();

                    foreach($products as $product) {
                        $lastProductId = $product->getAttribute('id');
                        $categoryName = $product->getAttribute('category');
                        if ($categoryName !== null) {
                            $category = Category::createOrUpdate($currentClientId, $categoryName);
                            $product->setAttribute('category_id', $category->getAttribute('id'));
                            $product->save();
                        }
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
