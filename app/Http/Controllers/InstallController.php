<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Businesses\InstallBusiness;
use App\Enums\CountryEnum;
use App\Helpers\LoggerHelper;
use App\Helpers\WebHookHelper;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class InstallController extends Controller
{
    public function __construct(
        private readonly InstallBusiness $installBusiness,
        private readonly ServiceRepository $serviceRepository,
    ) {
    }
    public function install(string $countryCode, string $serviceUrlPath, Request $request): Response
    {
        $country = CountryEnum::getByValue($countryCode);
        $code = $request->input('code');
        if ($code === null) {
            return Response('Bad request', 400);
        }

        try {
            $service = $this->serviceRepository->getByUrlPath($serviceUrlPath);
        } catch (Throwable) {
            abort(404, __('general.wrong_url'));
        }

        $clientService = $this->installBusiness->install($country, $code, $service);

        $webhookResponse = WebHookHelper::webhookResolver($clientService);
        if ($webhookResponse->failed()) {
            LoggerHelper::log('Webhook failed: ' . $webhookResponse->body() . ', Status code: ' . $webhookResponse->status());
        }
        return Response('ok', 200);
    }

    public function deactivate(string $serviceUrlPath): Response
    {
        try {
            $service = $this->serviceRepository->getByUrlPath($serviceUrlPath);
        } catch (Throwable) {
            abort(404, __('general.wrong_url'));
        }

        $this->installBusiness->deactivate($service);

        return Response('ok', 200);
    }

    public function uninstall(string $serviceUrlPath): Response
    {
        try {
            $service = $this->serviceRepository->getByUrlPath($serviceUrlPath);
        } catch (Throwable) {
            abort(404, __('general.wrong_url'));
        }

        $this->installBusiness->uninstall($service);

        return Response('ok', 200);
    }

    public function activate(string $serviceUrlPath): Response
    {
        try {
            $service = $this->serviceRepository->getByUrlPath($serviceUrlPath);
        } catch (Throwable) {
            abort(404, __('general.wrong_url'));
        }

        $this->installBusiness->activate($service);

        return Response('ok', 200);
    }
}
