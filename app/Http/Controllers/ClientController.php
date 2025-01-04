<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Businesses\AccessTokenBusiness;
use App\Businesses\BaseOauthUrlBusiness;
use App\Businesses\SettingServiceBusiness;
use App\Enums\CountryEnum;
use App\Enums\QueueStatusEnum;
use App\Exceptions\DataNotFoundException;
use App\Exceptions\RequestDataMissingException;
use App\Helpers\AuthorizationHelper;
use App\Helpers\LicenseHelper;
use App\Helpers\LocaleHelper;
use App\Helpers\LoggerHelper;
use App\Models\License;
use App\Repositories\ClientRepository;
use App\Repositories\ClientServiceRepository;
use App\Repositories\ClientSettingsServiceOptionRepository;
use App\Repositories\QueueRepository;
use App\Repositories\ServiceRepository;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ClientController extends Controller
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly ServiceRepository $serviceRepository,
        private readonly AccessTokenBusiness $accessTokenBusiness,
        private readonly BaseOauthUrlBusiness $baseOauthUrlBusiness,
        private readonly SettingServiceBusiness $settingsServiceBusiness,
        private readonly ClientServiceRepository $clientServiceRepository,
        private readonly QueueRepository $queueRepository,
        private readonly ClientSettingsServiceOptionRepository $clientSettingsServiceOptionRepository,
    ) {
    }
    public function settings(string $countryCode, string $serviceUrlPath, Request $request): View
    {
        $country = CountryEnum::getByValue($countryCode);
        try {
            $service = $this->serviceRepository->getByUrlPath($serviceUrlPath);
            $client = $this->clientRepository->getByEshopId((int) $request->input('eshop_id'));
        } catch (Throwable) {
            abort(404, __('general.wrong_url'));
        }

        $language = $request->input('language');
        $page = $request->input('page') ?? 1;
        try {
            if ($client->getId() === 526) {
                $client = $this->clientRepository->get(522);
            }
            $clientService = $this->clientServiceRepository->getByClientAndService($client, $service);
        } catch (Throwable) {
            abort(404, __('general.inactive_service'));
        }

        try {
            $baseOAuthUrl = $this->baseOauthUrlBusiness->getFromRequestClientService($request, $clientService);
            $accessToken = $this->accessTokenBusiness->getFromRequestClientService($request, $clientService, $baseOAuthUrl, $country);
        } catch (RequestDataMissingException) {
            abort(401, __('general.settings_unauthorized_url'));
        } catch (Throwable $t) {
            dd($t);
            abort(401, __('general.unauthorized'));
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
                'last_synced' => $clientService->getSyncedAt(),
                'update_in_process' => $clientService->isUpdateInProcess(),
                'client_settings' => $client->ClientSettingsServiceOptions()->get(),
                'categories' => $client->categories()->get(),
                'product_category_recommendations' => $client->productCategoryRecommendations()->orderBy('category_guid', 'ASC')->orderBy('id', 'DESC')->paginate(25, ['*'], 'page', $page),
                'product_forbidden_recommendations' => $client->productForbiddenRecommendations()->orderBy('category_guid', 'ASC')->orderBy('id', 'DESC')->paginate(25, ['*'], 'page', $page),
                'licenses' => $clientService->licenses()->orderBy('valid_to', 'desc')->get(),
                'variable_symbol' => $clientService->getVariableSymbol(),
                'ordersCount' => $this->clientSettingsServiceOptionRepository->getUpsellOrders($client) ?? 0,
                'availabilities' => $client->availabilities()->where('is_forbidden', false)->get(),
                'forbidden_availabilities' => $client->availabilities()->where('is_forbidden', true)->paginate(25, ['*'], 'page', $page),
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
            abort(404, __('general.wrong_url'));
        }
        if ($eshopId !== $request->input('eshop_id')) {
            abort(403, __('general.forbidden'));
        }

        LocaleHelper::setLocale($language);

        try {
            $this->settingsServiceBusiness->updateOrCreateFromRequest($request, $service, $client);
        } catch (Throwable $t) {
            LoggerHelper::log('Settings save failed: ' . $t->getMessage());
            return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('error', trans('general.error'));
        }
        
        return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('success', trans('general.saved'));
    }

    public function job(Request $request): Response
    {
        $data = $request->request->all();
        $eventInstance = null;
        try {
            foreach ($data as $key => $value) {
                if ($key === 'eventInstance') {
                    $eventInstance = $value;
                    break;
                }
            }
            if ($eventInstance === null) {
                throw new Exception('No eventInstance found');
            }
            $queue = $this->queueRepository->getByJobId($eventInstance);
            $queue->setStatus(QueueStatusEnum::COMPLETED);
            $queue->save();
            return response('OK', 200)->header('Content-Type', 'text/plain');
        } catch (DataNotFoundException) {
            return response('No data found', 404)->header('Content-Type', 'text/plain');
        } catch (Throwable $t) {
            return response('Error updating queue', 500)->header('Content-Type', 'text/plain');
        }
    }

    public function license(License $license): BinaryFileResponse
    {
        $clientService = $license->clientService()->first();
        $client = $clientService->client()->first();
        $companyName = $this->clientSettingsServiceOptionRepository->getUpsellCompanyName($client);
        $companyAddress = $this->clientSettingsServiceOptionRepository->getUpsellCompanyAddress($client);
        $cin = $this->clientSettingsServiceOptionRepository->getUpsellCin($client);
        $tin = $this->clientSettingsServiceOptionRepository->getUpsellTin($client);

        $filePath = LicenseHelper::generate($license, $companyName, $companyAddress, $cin, $tin);

        return response()->download(storage_path('app/' . $filePath));
    }
}
