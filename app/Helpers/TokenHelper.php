<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Exceptions\ApiAccessTokenNotFoundException;
use App\Models\Client;
use Exception;

class TokenHelper
{
    public static function getApiAccessToken(Client $client): string
    {
        $apiAccessTokenUrl = env('SHOPTET_API_ACCESS_TOKEN_URL');

        $OauthAccessToken = $client->getAttribute('oauth_access_token');
        $curl = curl_init($apiAccessTokenUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $OauthAccessToken]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, TRUE);
        foreach ($response as $key => $value) {
            LoggerHelper::log($key . ' => ' . $value);
        }
        if (ArrayHelper::containsKey($response, 'access_token') === false) {
            throw new ApiAccessTokenNotFoundException(new Exception('Api access token not found in response: ' . (string) $response));
        }
        return $response['access_token'];
    }
}