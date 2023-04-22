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
    protected $signature = 'update:clients';

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
        $clients = Client::get();
        foreach ($clients as $client) {
            $this->info('Updating images for client ' . $client->getAttribute('eshop_name'));
            try {
                $apiAccessToken = $client->getAccessToken();
                $clientResponse = ConnectorHelper::getEshop($apiAccessToken);
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
            } catch (Throwable $t) {
                $this->error('Error updating client ' . $t->getMessage());
                $client->setAttribute('status', ClientStatusEnum::INACTIVE);
                LoggerHelper::log('Error updating client ' . $t->getMessage());
            }

            $client->save();
        }
        return Command::SUCCESS;
    }
}
