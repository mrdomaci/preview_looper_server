<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Businesses\InstallBusiness;
use App\Enums\CountryEnum;
use App\Repositories\ClientRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class InstallController extends Controller
{
    public function __construct(
        private readonly InstallBusiness $installBusiness,
        private readonly ServiceRepository $serviceRepository,
        private readonly ClientRepository $clientRepository,
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

        $this->installBusiness->install($country, $code, $service);
        return Response('ok', 200);
    }

    public function deactivate(string $serviceUrlPath, Request $request): Response
    {
        try {
            $service = $this->serviceRepository->getByUrlPath($serviceUrlPath);
        } catch (Throwable) {
            abort(404, __('general.wrong_url'));
        }

        $data = $request->request->all();
        $eshopId = $data['eshopId'];
        $client = $this->clientRepository->getByEshopId((int) $eshopId);

        $this->installBusiness->deactivate($service, $client);

        return Response('ok', 200);
    }

    public function uninstall(string $serviceUrlPath, Request $request): Response
    {
        try {
            $service = $this->serviceRepository->getByUrlPath($serviceUrlPath);
        } catch (Throwable) {
            abort(404, __('general.wrong_url'));
        }


        $data = $request->request->all();
        $eshopId = $data['eshopId'];
        $client = $this->clientRepository->getByEshopId((int) $eshopId);

        $this->installBusiness->uninstall($service, $client);

        return Response('ok', 200);
    }

    public function activate(string $serviceUrlPath, Request $request): Response
    {
        try {
            $service = $this->serviceRepository->getByUrlPath($serviceUrlPath);
        } catch (Throwable) {
            abort(404, __('general.wrong_url'));
        }

        $data = $request->request->all();
        $eshopId = $data['eshopId'];
        $client = $this->clientRepository->getByEshopId((int) $eshopId);

        $this->installBusiness->activate($service, $client);

        return Response('ok', 200);
    }
}
