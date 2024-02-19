<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Helpers\WebHookHelper;
use App\Models\Service;
use App\Repositories\ClientServiceRepository;
use Illuminate\Console\Command;
use Throwable;

class WebhookUpdateOrdersByClientsCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:update:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update orders by webhook';

    public function __construct(
        private readonly ClientServiceRepository $clientServiceRepository,
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
        $dateLastSync = now()->subHours(2);
        try {
            $clientService = $this->clientServiceRepository->getNextForUpdate(Service::getUpsell(), $dateLastSync);
        } catch (Throwable) {
            $this->info('No orders to update');
            return Command::SUCCESS;
        }
        if ($clientService === null) {
            $this->info('No orders to update');
            return Command::SUCCESS;
        }
        $client = $clientService->client()->first();
        WebHookHelper::jenkinsWebhookUpdateOrders($client);
        $this->info('Client ' . (string) $client->getId() . ' webhooked to be orders updated');
        return Command::SUCCESS;
    }
}
