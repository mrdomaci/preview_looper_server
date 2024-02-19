<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Exceptions\ApiRequestFailException;
use App\Helpers\ConnectorHelper;
use App\Helpers\SessionHelper;
use App\Models\ClientService;
use Exception;
use Illuminate\Http\Request;

class BaseOauthUrlBusiness
{
    public function getFromRequestClientService(Request $request, ClientService $clientService): string
    {
        $client = $clientService->client()->first();
        $service = $clientService->service()->first();
        if (SessionHelper::hasBaseOauthUrl($request, $client, $service) === false) {
            $eshopResponse = ConnectorHelper::getEshop($clientService);
            if ($eshopResponse->getOauthUrl() === null) {
                throw new ApiRequestFailException(new Exception('Base OAuth URL not found in session or response for client ' . $client->getId()));
            }
            SessionHelper::setBaseOauthUrl($request, $client, $service, $eshopResponse->getOauthUrl());
        }
        return SessionHelper::getBaseOauthUrl($request, $service, $client);
    }
}
