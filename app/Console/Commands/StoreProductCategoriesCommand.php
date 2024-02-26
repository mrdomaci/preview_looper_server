<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Businesses\ProductCategoryBusiness;
use App\Models\ClientService;
use App\Models\Service;
use App\Repositories\ClientServiceRepository;
use Illuminate\Console\Command;

class StoreProductCategoriesCommand extends AbstractClientServiceCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:product-categories {--client=} {--service=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store product categories from products';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly ProductCategoryBusiness $productCategoryBusiness,
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
        $lastClientServiceId = 0;
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clientServices = $this->clientServiceRepository->getActive(
                $lastClientServiceId,
                Service::getUpsell(),
                $this->findClient(),
                $this->getIterationCount(),
            );

            /** @var ClientService $clientService */
            foreach ($clientServices as $clientService) {
                $lastClientServiceId = $clientService->getId();
                $this->productCategoryBusiness->createOrUpdate($clientService->client()->first());
            }
        }

        return Command::SUCCESS;
    }
}
