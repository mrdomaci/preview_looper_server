<?php

namespace App\Console\Commands;

use App\Businesses\ClientServiceBusiness;
use App\Models\Client;
use App\Repositories\ClientRepository;
use App\Repositories\ImageRepository;
use App\Repositories\ProductRepository;
use Illuminate\Console\Command;

class DeleteProductsApiCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:products {client_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete products for inactive clients';

    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly ClientServiceBusiness $clientServiceBusiness,
        private readonly ProductRepository $productRepository,
        private readonly ImageRepository $imageRepository
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

        $lastClientId = 0;
        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            $clients = $this->clientRepository->getClients($lastClientId, $clientId);
            /** @var Client $client */
            foreach ($clients as $client) {
                $shouldDelete = !$this->clientServiceBusiness->hasActiveService($client);
                if ($shouldDelete === true) {
                    continue;
                }
                $this->productRepository->deleteByClient($client);
                $this->imageRepository->deleteByClient($client);
                $lastClientId = $client->getId();
            }

            if (count($clients) < $this->getIterationCount()) {
                break;
            }
        }
        return Command::SUCCESS;
    }
}
