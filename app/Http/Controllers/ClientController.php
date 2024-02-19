<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Businesses\AccessTokenBusiness;
use App\Businesses\BaseOauthUrlBusiness;
use App\Businesses\SettingServiceBusiness;
use App\Businesses\SyncEndpointBusiness;
use App\Businesses\TemplateIncludeBusiness;
use App\Enums\CountryEnum;
use App\Helpers\AuthorizationHelper;
use App\Helpers\LocaleHelper;
use App\Helpers\LoggerHelper;
use App\Repositories\ClientRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly ServiceRepository $serviceRepository,
        private readonly AccessTokenBusiness $accessTokenBusiness,
        private readonly BaseOauthUrlBusiness $baseOauthUrlBusiness,
        private readonly SettingServiceBusiness $settingsServiceBusiness,
        private readonly TemplateIncludeBusiness $templateIncludeBusiness,
        private readonly SyncEndpointBusiness $syncEndpointBusiness,
    ) {
    }
    public function settings(string $countryCode, string $serviceUrlPath, Request $request): View
    {
        $country = CountryEnum::getByValue($countryCode);
        try {
            $service = $this->serviceRepository->getByUrlPath($serviceUrlPath);
            $client = $this->clientRepository->getByEshopId((int) $request->input('eshop_id'));
        } catch (Throwable) {
            abort(404);
        }

        $language = $request->input('language');
        $clientService = $client->services()->where('service_id', $service->getId())->first();

        try {
            $baseOAuthUrl = $this->baseOauthUrlBusiness->getFromRequestClientService($request, $clientService);
            $accessToken = $this->accessTokenBusiness->getFromRequestClientService($request, $clientService, $baseOAuthUrl, $country);
        } catch (Throwable) {
            abort(401);
        }

        $checkEshopId = AuthorizationHelper::getEshopId($accessToken, $baseOAuthUrl);
        if ($checkEshopId !== $client->getEshopId()) {
            LoggerHelper::log('Eshop ID mismatch for client ' . $client->getId() . ' from DB ' . $client->getEshopId() . ' from API ' . $checkEshopId);
            //loosen security for now
            //abort(403);
        }

        LocaleHelper::setLocale($language);

        return view(
            $service->getViewName() . '.settings',
            [
                'country' => $country->value,
                'service' => $service,
                'language' => $language,
                'client' => $client,
                'settings_service' => $service->settingsServices()->get(),
                'last_synced' => $clientService->getLastSyncedAt(),
                'update_in_process' => $clientService->isUpdateInProcess(),
                'client_settings' => $client->ClientSettingsServiceOptions()->get(),
                'categories' => $client->categories()->get(),
                'product_category_recommendations' => $client->productCategoryRecommendations()->get(),
            ]
        );
    }

    public function saveSettings(string $countryCode, string $serviceUrlPath, string $language, string $eshopId, Request $request): RedirectResponse
    {
        $country = CountryEnum::getByValue($countryCode);
        try {
            $service = $this->serviceRepository->getByUrlPath($serviceUrlPath);
            $client = $this->clientRepository->getByEshopId((int) $request->input('eshop_id'));
        } catch (Throwable) {
            abort(404);
        }
        if ($eshopId !== $request->input('eshop_id')) {
            abort(403);
        }

        LocaleHelper::setLocale($language);

        try {
            $this->settingsServiceBusiness->updateOrCreateFromRequest($request, $service, $client);
            $this->templateIncludeBusiness->post($service, $client);
        } catch (Throwable $t) {
            LoggerHelper::log('Settings save failed: ' . $t->getMessage());
            return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('error', trans('general.error'));
        }
        
        return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('success', trans('general.saved'));
    }

    public function sync(string $countryCode, string $serviceUrlPath, string $language, string $eshopId, Request $request): \Illuminate\Http\RedirectResponse
    {
        $country = CountryEnum::getByValue($countryCode);
        try {
            $service = $this->serviceRepository->getByUrlPath($serviceUrlPath);
            $client = $this->clientRepository->getByEshopId((int) $request->input('eshop_id'));
        } catch (Throwable) {
            abort(404);
        }
        try {
            $this->syncEndpointBusiness->syncClientService($client, $service);
        } catch (Throwable $t) {
            LoggerHelper::log('Webhook failed: ' . $t->getMessage());
            return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('error', trans('general.error'));
        }

        return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('success', trans('general.synced_scheduled'));
    }
}
