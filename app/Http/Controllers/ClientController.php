<?php

namespace App\Http\Controllers;

use App\Enums\ClientServiceStatusEnum;
use App\Exceptions\ApiRequestFailException;
use App\Helpers\AuthorizationHelper;
use App\Helpers\ConnectorBodyHelper;
use App\Helpers\ConnectorHelper;
use App\Helpers\LocaleHelper;
use App\Helpers\LoggerHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\WebHookHelper;
use App\Models\Client;
use App\Models\ClientService;
use App\Models\ClientSettingsServiceOption;
use App\Models\Service;
use App\Models\SettingsService;
use App\Models\SettingsServiceOption;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    public function install(string $country, string $serviceUrlPath, Request $request): Response
    {
        $country = strtoupper($country);
        $code = $request->input('code');
        if ($code === NULL) {
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

        $webhookResponse = WebHookHelper::jenkinsWebhookClient($client->getAttribute('id'));
        if ($webhookResponse->failed()) {
            LoggerHelper::log('Webhook failed: ' . $webhookResponse->body() . ', Status code: ' . $webhookResponse->status());
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
        $client = Client::getByEshopId($eshopId);

        ClientService::updateStatus($client, $service, ClientServiceStatusEnum::INACTIVE);

        return Response('ok', 200);
    }

    public function uninstall(string $serviceUrlPath): Response
    {
        $service = Service::where('url-path', $serviceUrlPath)->first();
        if ($service === null) {
            abort(404);
        }
        $eshopId = WebHookHelper::getEshopId(WebHookHelper::EVENT_UNINSTALL);
        $client = Client::getByEshopId($eshopId);

        ClientService::updateStatus($client, $service, ClientServiceStatusEnum::DELETED);

        return Response('ok', 200);
    }

    public function activate(string $serviceUrlPath): Response
    {
        $service = Service::where('url-path', $serviceUrlPath)->first();
        if ($service === null) {
            abort(404);
        }
        $eshopId = WebHookHelper::getEshopId(WebHookHelper::EVENT_UNINSTALL);
        $client = Client::getByEshopId($eshopId);

        ClientService::updateStatus($client, $service, ClientServiceStatusEnum::ACTIVE);
        return Response('ok', 200);
    }

    public function settings(string $country,  string $serviceUrlPath, Request $request): View
    {
        $country = strtoupper($country);
        $service = Service::where('url-path', $serviceUrlPath)->first();
        if ($service === null) {
            abort(404);
        }
        $language = $request->input('language');
        $eshopId = $request->input('eshop_id');

        $client = Client::getByEshopId((int) $eshopId);
        $serviceSettings = SettingsService::where('service_id', $service->getAttribute('id'))->orderBy('sort')->get();
        $clientService = ClientService::where('client_id', $client->getAttribute('id'))->where('service_id', $service->getAttribute('id'))->first();
        if ($request->session()->has($eshopId . '_access_token') === false) {
            $code = $request->input('code');
            $eshopResponse = ConnectorHelper::getEshop($clientService);
            $baseOAuthUrl = null;
            if ($eshopResponse->getOauthUrl() !== null) {
                $baseOAuthUrl = $eshopResponse->getOauthUrl();
                session([$eshopId . '_base_oauth_url' => $baseOAuthUrl]);
            }
            if ($baseOAuthUrl === null) {
                $baseOAuthUrl = session($eshopId . '_base_oauth_url');
            }
            if ($baseOAuthUrl === null) {
                throw new ApiRequestFailException(new Exception('Base OAuth URL not found in session or response for client ' . $client->getAttribute('eshop_id')));
            }
    
            $accessToken = AuthorizationHelper::getAccessTokenForSettings($country, $code, $serviceUrlPath, $eshopId, $language, $baseOAuthUrl);
            $request->session()->put($eshopId . '_access_token', $accessToken);   
            $request->session()->put($eshopId . '_base_oauth_url', $baseOAuthUrl);
        } else {
            $accessToken = $request->session()->get($eshopId . '_access_token');
            $baseOAuthUrl = $request->session()->get($eshopId . '_base_oauth_url');
        }

        $checkEshopId = AuthorizationHelper::getEshopId($accessToken, $baseOAuthUrl);
        LocaleHelper::setLocale($language);
        if ($checkEshopId !== $client->getAttribute('eshop_id')) {
            LoggerHelper::log('Eshop ID mismatch for client ' . $client->getAttribute('id') . ' from DB ' . $client->getAttribute('eshop_id') . ' from API ' . $checkEshopId); 
            abort(401);
        }
        return view($service->getAttribute('view-name') . '.settings',
            [
                'country' => $country,
                'service_url_path' => $serviceUrlPath,
                'language' => $language,
                'client' => $client,
                'settings_service' => $serviceSettings
            ]);
    }

    public function saveSettings(string $country, string $serviceUrlPath, string $language, string $eshopId, Request $request): \Illuminate\Http\RedirectResponse
    {
        $country = strtoupper($country);
        $service = Service::where('url-path', $serviceUrlPath)->first();
        if ($service === null) {
            abort(404);
        }
        if ($eshopId !== $request->input('eshop_id')) {
            abort(403);
        }
        $client = Client::getByEshopId((int) $eshopId);
        $settingsServices = SettingsService::where('service_id', $service->getAttribute('id'))->get();
        foreach ($settingsServices as $settingsService) {
            $selectedOption = $request->input($settingsService->getAttribute('id'));
            $settingsServiceOption = SettingsServiceOption::where('id', $selectedOption)->first();
            ClientSettingsServiceOption::updateOrCreate($client, $settingsService, $settingsServiceOption);
        }
        LocaleHelper::setLocale($language);
        $client = Client::getByEshopId((int) $eshopId);
        $clientService = ClientService::where('client_id', $client->getAttribute('id'))->where('service_id', $service->getAttribute('id'))->first();
        $body = ConnectorBodyHelper::getStringBodyForTemplateInclude($service, $client);
        $templateIncludeResponse = ConnectorHelper::postTemplateInclude($clientService, $body);
        if ($templateIncludeResponse->getTemplateIncludes() === []) {
            LoggerHelper::log('Template include failed for client ' . $client->getAttribute('eshop_id'));
            return redirect()->route('client.settings', ['country' => $country, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('error', trans('general.error'));
        }
        return redirect()->route('client.settings', ['country' => $country, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('success', trans('general.saved'));
    }
}
