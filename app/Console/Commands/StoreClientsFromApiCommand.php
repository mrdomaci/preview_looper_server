<?php

namespace App\Console\Commands;

use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\AddonInstallFailException;
use App\Helpers\ConnectorHelper;
use App\Helpers\LoggerHelper;
use App\Models\Client;
use Illuminate\Console\Command;
use Throwable;

class StoreClientsFromApiCommand extends AbstractCommand
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
        $success = true;
        $clientId = $this->argument('client_id');

        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            if ($clientId !== null) {
                $client = Client::where('id', $clientId)->first();
                $clients = [$client];
            } else {
                $clients = Client::limit($this->getIterationCount())
                    ->offset($this->getOffset($i))
                    ->get();
            }
            /** @var Client $client */
            foreach ($clients as $client) {
                $update = false;
                $clientService = null;
                $clientServices = $client->services();
                foreach ($clientServices->get() as $clientService) {
                    if ($clientService->getAttribute('status') === ClientServiceStatusEnum::ACTIVE) {
                        $update = true;
                        break;
                    }
                }
                if ($update === false) {
                    continue;
                }
                if ($clientService === null) {
                    continue;
                }

                try {
                    $clientResponse = ConnectorHelper::getEshop($clientService);
                    $client->setAttribute('eshop_name', $clientResponse->getName());
                    $client->setAttribute('url', $clientResponse->getUrl());
                    $client->setAttribute('eshop_category', $clientResponse->getCategory());
                    $client->setAttribute('eshop_subtitle', $clientResponse->getSubtitle());
                    $client->setAttribute('contact_person', $clientResponse->getContactPerson());
                    $client->setAttribute('email', $clientResponse->getEmail());
                    $client->setAttribute('phone', $clientResponse->getPhone());
                    $client->setAttribute('street', $clientResponse->getStreet());
                    $client->setAttribute('city', $clientResponse->getCity());
                    $client->setAttribute('zip', $clientResponse->getZip());
                    $client->setAttribute('country', $clientResponse->getCountry());
                    $client->setAttribute('last_synced_at', now());
                    
                    $this->info('Updating client id:' . (string) $client->getAttribute('id'));
                } catch (AddonInstallFailException) {
                    $clientService->setAttribute('status', ClientServiceStatusEnum::INACTIVE);
                    $clientService->save();
                } catch (Throwable $t) {
                    $this->error('Error updating client ' . $t->getMessage());
                    LoggerHelper::log('Error updating client ' . $t->getMessage());
                    $success = false;
                }

                $client->save();
            }

            if (count($clients) < $this->getIterationCount()) {
                break;
            }
        }
        if ($success === true) {
            return Command::SUCCESS;
        } else {
            return Command::FAILURE;
        }
    }
}
