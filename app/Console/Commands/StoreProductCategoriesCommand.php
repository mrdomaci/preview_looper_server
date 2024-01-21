<?php

namespace App\Console\Commands;

use App\Models\ClientService;
use App\Models\Service;
use App\Repositories\ClientServiceRepository;
use App\Repositories\ProductRepository;
use Illuminate\Console\Command;

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

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly ProductRepository $productRepository
        )
    {
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
        
        $lastClientServiceId = 0;
        for($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = $this->clientServiceRepository->getActive(
                $lastClientServiceId,
                Service::getUpsell(),
                $clientId,
                $this->getIterationCount(),
            );

            /** @var ClientService $clientService */
            foreach ($clientServices as $clientService) {
                $lastClientServiceId = $clientService->getAttribute('id');
                $client = $clientService->client()->first();

                $lastProductId = 0;
                for($j = 0; $j < $this->getMaxIterationCount(); $j++) {
                    foreach($this->productRepository->getProductsPastId($client, $lastProductId) as $product) {
                        $lastProductId = $product->getAttribute('id');
                        $this->productRepository->setProductCategory($product);
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
