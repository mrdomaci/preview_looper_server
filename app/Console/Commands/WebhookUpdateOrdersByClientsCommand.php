<?php

namespace App\Console\Commands;

use App\Enums\ClientServiceStatusEnum;
use App\Helpers\WebHookHelper;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Service;
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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dateLastSync = now()->subHours(2);
        $service = Service::find(Service::DYNAMIC_PREVIEW_IMAGES);
        try {
            $clientServices = ClientService::where('service_id', $service->getAttribute('id'))
                                ->where('status', ClientServiceStatusEnum::ACTIVE)
                                ->where('date_last_synced', '<=', $dateLastSync)
                                ->where('update_in_process', '=', 0)
                                ->where('service_id', Service::ORDER_STATUS)
                                ->first();
        } catch (Throwable) {
            $this->info('No clients to update');
            return Command::SUCCESS;
        }
        if ($clientServices === null) {
            $this->info('No clients to update');
            return Command::SUCCESS;
        }
        $client = Client::find($clientServices->getAttribute('client_id'));
        WebHookHelper::jenkinsWebhookUpdateOrders($client->getAttribute('id'));
        $this->info('Client ' . (string) $client->getAttribute('id') . ' webhooked to be updated');
        return Command::SUCCESS;
    }
}