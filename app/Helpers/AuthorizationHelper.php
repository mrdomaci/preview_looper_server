<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Exceptions\AddonInstallFailException;
use App\Exceptions\AddonSettingsSecurityFailException;
use Exception;
use Nette\Utils\Json;

class AuthorizationHelper
{
    /**
     * @param string $code
     * @return array<string, int|string>
     * @throws AddonInstallFailException
     */
    public static function getResponseForInstall(string $code): array
    {
        $data = [
            'client_id' => env('SHOPTET_CLIENT_ID'),
            'client_secret' => env('SHOPTET_CLIENT_SECRET'), 
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => Route('client.install'),
            'scope' => 'api',
        ];

        $curl = curl_init(env('SHOPTET_OAUTH_SERVER_TOKEN_URL'));
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        $response = curl_exec($curl);
        curl_close($curl);

        $response = Json::decode($response, true);
        if (ArrayHelper::containsKey($response, 'error') === true) {
            throw new AddonInstallFailException(new Exception($response['error'] . ': ' . $response['error_description']));
        }
        return $response;
    }

    public static function getAccessTokenForSettings(string $code, string $eshopId, string $language, ?string $baseOAuthUrl): string
    {
        $data = [
            'client_id' => env('SHOPTET_CLIENT_ID'),
            'client_secret' => env('SHOPTET_CLIENT_SECRET'), 
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => Route('client.settings'),
            'scope' => 'basic_eshop',
        ];
        
        LoggerHelper::log(Route('client.settings'));

        $url = $baseOAuthUrl . 'token';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
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
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
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