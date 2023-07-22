<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Exceptions\ApiAccessTokenNotFoundException;
use App\Exceptions\ApiRequestFailException;
use App\Models\ClientService;
use Exception;

class TokenHelper
{
    public static function getApiAccessToken(ClientService $clientService): string
    {
        $apiAccessTokenUrl = env('SHOPTET_API_ACCESS_TOKEN_URL');

        $OauthAccessToken = $clientService->getAttribute('oauth_access_token');
        $curl = curl_init($apiAccessTokenUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $OauthAccessToken]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, TRUE);
        if (ArrayHelper::containsKey($response, 'error') === true) {
            throw new ApiRequestFailException(new Exception('Error in response requesting api access token [' . $response['error'] . ']: ' . $response['error_description']));
        }
        if (ArrayHelper::containsKey($response, 'access_token') === false) {
            throw new ApiAccessTokenNotFoundException(new Exception('Api access token not found in response: ' . (string) $response));
        }
        return $response['access_token'];
    }
}