<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Enums\CountryEnum;
use App\Exceptions\AddonNotInstalledException;
use App\Exceptions\AddonSuspendedException;
use App\Exceptions\ApiAccessTokenNotFoundException;
use App\Exceptions\ApiRequestFailException;
use App\Models\ClientService;
use Exception;

class TokenHelper
{
    public static function getApiAccessToken(ClientService $clientService): string
    {
        $country = CountryEnum::getByValue($clientService->getCountry());

        $OauthAccessToken = $clientService->getOAuthAccessToken();
        $curl = curl_init($country->getApiAccessTokenUrl());
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $OauthAccessToken]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        if ($response === false) {
            throw new ApiRequestFailException(new Exception('Failed to request api access token: ' . curl_error($curl)));
        }
        curl_close($curl);
        $response = json_decode($response, true);
        if (ArrayHelper::containsKey($response, 'error') === true) {
            if ($response['error'] === 'addon_not_installed') {
                throw new AddonNotInstalledException('Addon not installed', 401);
            }
            if ($response['error'] === 'addon_suspended') {
                throw new AddonSuspendedException('Addon suspended', 401);
            }
            throw new ApiRequestFailException(new Exception('Error in response requesting api access token [' . $response['error'] . ']: ' . $response['error_description']));
        }
        if (ArrayHelper::containsKey($response, 'access_token') === false) {
            throw new ApiAccessTokenNotFoundException(new Exception('Api access token not found in response: ' . (string) $response));
        }
        return $response['access_token'];
    }
}
