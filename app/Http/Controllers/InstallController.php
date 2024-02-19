<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ClientServiceStatusEnum;
use App\Helpers\AuthorizationHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\WebHookHelper;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\Service;
use App\Repositories\ClientRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InstallController extends Controller
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
    ) {
    }
    public function install(string $country, string $serviceUrlPath, Request $request): Response
    {
        $country = strtoupper($country);
        $code = $request->input('code');
        if ($code === null) {
            return Response('Bad request', 400);
        }

        $service = Service::where('url-path', $serviceUrlPath)->first();
        if ($service === null) {
            abort(404);
        }

        $response = AuthorizationHelper::getResponseForInstall($country, $code, $serviceUrlPath);

        $oAuthAccessToken = ResponseHelper::getAccessToken($response);
        $eshopId = ResponseHelper::getEshopId($response);
        $eshopUrl = ResponseHelper::getFromResponse($response, 'eshopUrl');
        $contactEmail = ResponseHelper::getFromResponse($response, 'contactEmail');
        
        $client = Client::updateOrCreate($eshopId, $eshopUrl, $contactEmail);
        ClientService::updateOrCreate($client, $service, $oAuthAccessToken, $country);

        if ($service->isDynamicPreviewImages()) {
            $webhookResponse = WebHookHelper::jenkinsWebhookClient($client->getId());
            if ($webhookResponse->failed()) {
                LoggerHelper::log('Webhook failed: ' . $webhookResponse->body() . ', Status code: ' . $webhookResponse->status());
            }
        }
        return Response('ok', 200);
    }

    public function deactivate(string $serviceUrlPath): Response
    {
        $service = Service::where('url-path', $serviceUrlPath)->first();
        if ($service === null) {
            abort(404);
        }
        $eshopId = WebHookHelper::getEshopId(WebHookHelper::EVENT_DEACTIVATE);
        $client = $this->clientRepository->getByEshopId($eshopId);
        ClientService::updateStatus($client, $service, ClientServiceStatusEnum::INACTIVE);
        LoggerHelper::log('Client ' . $client->getId() . ' deactivated');

        return Response('ok', 200);
    }

    public function uninstall(string $serviceUrlPath): Response
    {
        $service = Service::where('url-path', $serviceUrlPath)->first();
        if ($service === null) {
            abort(404);
        }
        $eshopId = WebHookHelper::getEshopId(WebHookHelper::EVENT_UNINSTALL);
        $client = $this->clientRepository->getByEshopId($eshopId);

        ClientService::updateStatus($client, $service, ClientServiceStatusEnum::DELETED);
        LoggerHelper::log('Client ' . $client->getId() . ' uninstalled');

        return Response('ok', 200);
    }

    public function activate(string $serviceUrlPath): Response
    {
        $service = Service::where('url-path', $serviceUrlPath)->first();
        if ($service === null) {
            abort(404);
        }
        $eshopId = WebHookHelper::getEshopId(WebHookHelper::EVENT_ACTIVATE);
        $client = $this->clientRepository->getByEshopId($eshopId);

        ClientService::updateStatus($client, $service, ClientServiceStatusEnum::ACTIVE);
        LoggerHelper::log('Client ' . $client->getId() . ' activated');
        WebHookHelper::jenkinsWebhookClient($client->getId());
        return Response('ok', 200);
    }
}
