<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Enums\ClientServiceStatusEnum;
use App\Enums\CountryEnum;
use App\Helpers\AuthorizationHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\WebHookHelper;
use App\Models\ClientService;
use App\Models\Service;
use App\Repositories\ClientRepository;
use App\Repositories\ClientServiceRepository;

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

    public function deactivate(Service $service): void
    {
        $eshopId = WebHookHelper::getEshopId(WebHookHelper::EVENT_DEACTIVATE);
        $client = $this->clientRepository->getByEshopId($eshopId);
        $this->clientServiceRepository->updateStatus($client, $service, ClientServiceStatusEnum::INACTIVE);
        LoggerHelper::log('Client ' . $client->getId() . ' deactivated');
    }

    public function uninstall(Service $service): void
    {
        $eshopId = WebHookHelper::getEshopId(WebHookHelper::EVENT_UNINSTALL);
        $client = $this->clientRepository->getByEshopId($eshopId);
        $this->clientServiceRepository->updateStatus($client, $service, ClientServiceStatusEnum::DELETED);
        LoggerHelper::log('Client ' . $client->getId() . ' uninstalled');
    }

    public function activate(Service $service): void
    {
        $eshopId = WebHookHelper::getEshopId(WebHookHelper::EVENT_ACTIVATE);
        $client = $this->clientRepository->getByEshopId($eshopId);
        $clientSevice = $this->clientServiceRepository->updateStatus($client, $service, ClientServiceStatusEnum::ACTIVE);
        LoggerHelper::log('Client ' . $client->getId() . ' activated');
        WebHookHelper::webhookResolver($clientSevice);
    }
}
