<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Exceptions\RequestDataMissingException;
use App\Helpers\AuthorizationHelper;
use App\Helpers\SessionHelper;
use App\Models\ClientService;
use App\Models\Service;
use Illuminate\Http\Request;

class AccessTokenBusiness
{
    public function getFromRequestClientService(Request $request, ClientService $clientService, string $baseOAuthUrl, string $country): string
    {
        $client = $clientService->client()->first();
        $service = $clientService->service()->first();
        if (SessionHelper::hasAccessToken($request, $client, $service) === false) {
            $code = $this->getCode($request);
            $accesToken = $this->get($country, $code, $service, $baseOAuthUrl);
            SessionHelper::setAccessToken($request, $client, $service, $accesToken);
        }
        return SessionHelper::getAccessToken($request, $service, $client);
    }

    private function get(string $country, string $code, Service $service, string $baseOAuthUrl): string
    {
        return AuthorizationHelper::getAccessTokenForSettings($country, $code, $service, $baseOAuthUrl);
    }

    private function getCode(Request $request): string
    {
        $code = $request->input('code');
        if ($code === null) {
            throw new RequestDataMissingException(new \Exception('Code is missing in request query parameters.'));
        }
        return $code;
    }
}
