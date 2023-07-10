<?php

namespace App\Console\Commands;

use App\Enums\ClientStatusEnum;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Models\Client;
use Illuminate\Console\Command;
use Throwable;

class StoreClientsFromApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:clients {client_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store clients from API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $clientId = $this->argument('client_id');
        if ($clientId !== null) {
            $client = Client::where('id', $clientId)->first();
            $clients = [$client];
        } else {
            $clients = Client::get();
        }
        $success = true;
        /** @var Client $client */
        foreach ($clients as $client) {
            $this->info('Updating client id:' . (string) $client->getAttribute('id'));
            try {
                $clientResponse = ConnectorHelper::getEshop($client);
                $client->setAttribute('eshop_name', $clientResponse->getName());
                $client->setAttribute('url', $clientResponse->getUrl());
                $client->setAttribute('eshop_category', $clientResponse->getCategory());
                $client->setAttribute('eshop_subtitle', $clientResponse->getSubtitle());
                $client->setAttribute('constact_person', $clientResponse->getContactPerson());
                $client->setAttribute('email', $clientResponse->getEmail());
                $client->setAttribute('phone', $clientResponse->getPhone());
                $client->setAttribute('street', $clientResponse->getStreet());
                $client->setAttribute('city', $clientResponse->getCity());
                $client->setAttribute('zip', $clientResponse->getZip());
                $client->setAttribute('country', $clientResponse->getCountry());
                $client->setAttribute('status', ClientStatusEnum::ACTIVE);
                $client->setAttribute('last_synced_at', now());
            } catch (Throwable $t) {
                $this->error('Error updating client ' . $t->getMessage());
                $client->setAttribute('status', ClientStatusEnum::INACTIVE);
                LoggerHelper::log('Error updating client ' . $t->getMessage());
                $success = false;
            }

            $client->save();
        }
        if ($success === true) {
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
