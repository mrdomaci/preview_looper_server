<?php

namespace App\Console\Commands;

use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\AddonNotInstalledException;
use App\Exceptions\AddonSuspendedException;
use App\Helpers\ConnectorHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\WebHookHelper;
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
        $clientId = $this->argument('client_id');

        for ($i = 0; $i < $this->getMaxIterationCount(); $i++) {
            if ($clientId !== null) {
                $clients = Client::where('id', $clientId)->get();
            } else {
                $clients = Client::limit($this->getIterationCount())
                    ->offset($this->getOffset($i))
                    ->get();
            }
            /** @var Client $client */
            foreach ($clients as $client) {
                $clientServices = $client->services()->first();
                if ($clientServices->getAttribute('date_last_synced') >= now()->subHours(12)) {
                    continue;
                }
                if ($clientServices->getAttribute('update_in_process') === true) {
                    continue;
                }
                $clientResponse = null;
                $clientServices = $client->services();
                $response = ResponseHelper::getUrlResponse($client->getAttribute('url'));
                $javascriptCode = ResponseHelper::extractShoptetScriptFromBody($response);
                $templateName = ResponseHelper::findTemplateName($javascriptCode);
                foreach ($clientServices->get() as $clientService) {
                    try {
                        $clientResponse = ConnectorHelper::getEshop($clientService);
                        $currentStatus = $clientService->getAttribute('status');
                        $clientService->setAttribute('status', ClientServiceStatusEnum::ACTIVE);
                        $clientService->save();
                        if ($currentStatus !== ClientServiceStatusEnum::ACTIVE) {
                            WebHookHelper::jenkinsWebhookUpdateClient($client->getAttribute('id'));
                        }
                    } catch (AddonNotInstalledException $e) {
                        $clientService->setAttribute('status', ClientServiceStatusEnum::DELETED);
                        $clientService->save();
                    } catch (AddonSuspendedException $e) {
                        $clientService->setAttribute('status', ClientServiceStatusEnum::INACTIVE);
                        $clientService->save();
                    } catch (Throwable $e) {
                        $this->error($e->getMessage());
                    }
                }
                if ($clientResponse === null) {
                    continue;
                }

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
                $client->setAttribute('template_name', $templateName);
                $client->setAttribute('last_synced_at', now());
                
                $this->info('Updating client id:' . (string) $client->getAttribute('id'));

                $client->save();
            }

            if (count($clients) < $this->getIterationCount()) {
                break;
            }
        }
        return Command::SUCCESS;
    }
}
