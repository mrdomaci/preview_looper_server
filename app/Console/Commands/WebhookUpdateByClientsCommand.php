<?php

namespace App\Console\Commands;

use App\Enums\ClientServiceStatusEnum;
use App\Helpers\WebHookHelper;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Service;
use Illuminate\Console\Command;
use Throwable;

class WebhookUpdateByClientsCommand extends AbstractCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:update:clients';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update clients by webhook';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dateLastSync = now()->subHours(12);
        $service = Service::find(Service::DYNAMIC_PREVIEW_IMAGES);
        try {
            $clientServices = ClientService::where('service_id', $service->getAttribute('id'))->where('status', ClientServiceStatusEnum::ACTIVE)->where('date_last_synced', '<=', $dateLastSync)->first();
        } catch (Throwable) {
            $this->info('No clients to update');
            return Command::SUCCESS;
        }
        if ($clientServices === null) {
            $this->info('No clients to update');
            return Command::SUCCESS;
        }
        $client = Client::find($clientServices->getAttribute('client_id'));
        WebHookHelper::jenkinsWebhookUpdateClient($client->getAttribute('id'));
        $this->info('Client ' . (string) $client->getAttribute('id') . ' webhooked to be updated');
        return Command::SUCCESS;
    }
}
