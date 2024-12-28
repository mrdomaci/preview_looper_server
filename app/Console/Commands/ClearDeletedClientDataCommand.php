<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Models\ClientService;
use App\Repositories\ClientServiceRepository;
use App\Repositories\OrderProductRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductCategoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Throwable;

class ClearDeletedClientDataCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:deleted-client-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear deleted client data';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly ProductRepository $productRepository,
        private readonly ProductCategoryRepository $productCategoryRepository,
        private readonly OrderRepository $orderRepository,
        private readonly OrderProductRepository $orderProductRepository,
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
        $clientServices = $this->clientServiceRepository->getDeleted();
        if ($clientServices->isEmpty()) {
            $this->info('No deleted client services');
            return Command::SUCCESS;
        }
        $success = true;
        /** @var Collection<ClientService> $clientServices */
        foreach ($clientServices as $clientService) {
            $this->info('Client service ' . $clientService->getId() . ' delete data started');
            $client = $clientService->client()->first();
            if ($client !== null) {
                try {
                    $this->productRepository->deleteByClient($client);
                    $this->productCategoryRepository->deleteByClient($client);
                    $this->orderRepository->deleteByClient($client);
                    $this->orderProductRepository->deleteByClient($client);
                } catch (Throwable $e) {
                    $success = false;
                }
            }
            $clientService->setQueueStatus(ClientServiceQueueStatusEnum::DELETED)->save();
        }
        return $success ? Command::SUCCESS : Command::FAILURE;
    }
}
