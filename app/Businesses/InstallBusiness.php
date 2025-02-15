<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Enums\ClientServiceQueueStatusEnum;
use App\Enums\ClientServiceStatusEnum;
use App\Enums\CountryEnum;
use App\Helpers\AuthorizationHelper;
use App\Helpers\FileHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Service;
use App\Repositories\ClientRepository;
use App\Repositories\ClientServiceRepository;
use DateTime;
use Throwable;

class InstallBusiness
{
    public function __construct(
        private ClientRepository $clientRepository,
        private ClientServiceRepository $clientServiceRepository,
    ) {
    }
    public function install(CountryEnum $country, string $code, Service $service): ClientService
    {
        $response = AuthorizationHelper::getResponseForInstall($country, $code, $service);

        $oAuthAccessToken = ResponseHelper::getAccessToken($response);
        $eshopId = ResponseHelper::getEshopId($response);
        $eshopUrl = ResponseHelper::getFromResponse($response, 'eshopUrl');
        $contactEmail = ResponseHelper::getFromResponse($response, 'contactEmail');
        
        $client = $this->clientRepository->updateOrCreate($eshopId, $eshopUrl, $contactEmail);
        return $this->clientServiceRepository->updateOrCreate($client, $service, $oAuthAccessToken, $country);
    }

    public function deactivate(Service $service, Client $client): void
    {
        $this->clientServiceRepository->updateStatus($client, $service, ClientServiceStatusEnum::INACTIVE);
        LoggerHelper::log('Client ' . $client->getId() . ' deactivated');
    }

    public function uninstall(Service $service, Client $client): void
    {
        $this->clientServiceRepository->updateStatus($client, $service, ClientServiceStatusEnum::DELETED);
        try {
            $clientService = $this->clientServiceRepository->getByClientAndService($client, $service);
            FileHelper::clearFiles($clientService);
        } catch (Throwable) {
            LoggerHelper::log('Client ' . $client->getId() . ' with service ' . $service->getId() . ' not found');
        }
        LoggerHelper::log('Client ' . $client->getId() . ' uninstalled');
    }

    public function activate(Service $service, Client $client): void
    {
        $clientService = $this->clientServiceRepository->updateStatus($client, $service, ClientServiceStatusEnum::ACTIVE);
        $clientService
            ->setQueueStatus(ClientServiceQueueStatusEnum::CLIENTS)
            ->setWebhookedAt(new DateTime())
            ->save();
        LoggerHelper::log('Client ' . $client->getId() . ' activated');
    }
}
