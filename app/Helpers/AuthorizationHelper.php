<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Exceptions\AddonInstallFailException;
use App\Exceptions\AddonSettingsSecurityFailException;
use Exception;
use Nette\Utils\Json;
use Throwable;

class AuthorizationHelper
{
    /**
     * @param string $code
     * @return array<string, int|string>
     * @throws AddonInstallFailException
     */
    public static function getResponseForInstall(string $country, string $code, string $serviceUrlPath): array
    {
        if ($country === 'HU') {
            $clientId = env('SHOPTET_CLIENT_ID_HU');
            $clientSecret = env('SHOPTET_CLIENT_SECRET_HU');
            $oauthServerTokenUrl = env('SHOPTET_OAUTH_SERVER_TOKEN_URL_HU');
        } else {
            $clientId = env('SHOPTET_CLIENT_ID_CZ');
            $clientSecret = env('SHOPTET_CLIENT_SECRET_CZ');
            $oauthServerTokenUrl = env('SHOPTET_OAUTH_SERVER_TOKEN_URL_CZ');
        }

        $data = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => Route('client.install', ['country' => $country, 'serviceUrlPath' => $serviceUrlPath]),
            'scope' => 'api',
        ];

        $curl = curl_init($oauthServerTokenUrl);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        $response = curl_exec($curl);
        curl_close($curl);
        try {
            $response = Json::decode($response, true);
        } catch (Throwable $t) {
            throw new AddonInstallFailException(new Exception('Error in response requesting api access token:' . $t->getMessage() . ' code: ' . $t->getCode() . ' response: ' . (string) $response));
        }
        if (ArrayHelper::containsKey($response, 'error') === true) {
            throw new AddonInstallFailException(new Exception($response['error'] . ': ' . $response['error_description']));
        }
        return $response;
    }

    public static function getAccessTokenForSettings(string $country, string $code, string $serviceUrlPath, string $eshopId, string $language, ?string $baseOAuthUrl): string
    {
        if ($country === 'HU') {
            $clientId = env('SHOPTET_CLIENT_ID_HU');
            $clientSecret = env('SHOPTET_CLIENT_SECRET_HU');
        } else {
            $clientId = env('SHOPTET_CLIENT_ID_CZ');
            $clientSecret = env('SHOPTET_CLIENT_SECRET_CZ');
        }
        $data = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => Route('client.settings', ['country' => $country, 'serviceUrlPath' => $serviceUrlPath]),
            'scope' => 'basic_eshop',
        ];
        
        $url = $baseOAuthUrl . 'token';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error !== '') {
            throw new AddonSettingsSecurityFailException(new Exception('Curl error: ' . $error));
        }

        $response = Json::decode($response, true);
        if (ArrayHelper::containsKey($response, 'error') === true) {
            throw new AddonSettingsSecurityFailException(new Exception($response['error'] . ': ' . $response['error_description']));
        }
        if (ArrayHelper::containsKey($response, 'access_token') === false) {
            throw new AddonSettingsSecurityFailException(new Exception('Missing access token'));
        }
        return $response['access_token'];
    }

    public static function getEshopId(string $accessToken, ?string $baseOAuthUrl): int
    {
        $url = $baseOAuthUrl . 'resource?method=getBasicEshop';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error !== '') {
            throw new AddonSettingsSecurityFailException(new Exception('Curl access token error: ' . $error));
        }

        $response = Json::decode($response, true);
        if (ArrayHelper::containsKey($response, 'error') === true) {
            throw new AddonSettingsSecurityFailException(new Exception($response['error'] . ': ' . $response['error_description']));
        }
        if (ArrayHelper::containsKey($response, 'data') === false) {
            throw new AddonSettingsSecurityFailException(new Exception('Missing data'));
        }
        if (ArrayHelper::containsKey($response['data'], 'project') === false) {
            throw new AddonSettingsSecurityFailException(new Exception('Missing project'));
        }
        if (ArrayHelper::containsKey($response['data']['project'], 'id') === false) {
            throw new AddonSettingsSecurityFailException(new Exception('Missing project id'));
        }
        return (int)$response['data']['project']['id'];
    }
}
