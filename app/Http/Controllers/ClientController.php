<?php

namespace App\Http\Controllers;

use App\Enums\ClientStatusEnum;
use App\Exceptions\ApiRequestFailException;
use App\Helpers\ArrayHelper;
use App\Helpers\AuthorizationHelper;
use App\Helpers\ConnectorHelper;
use App\Helpers\LocaleHelper;
use App\Helpers\NumbersHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\WebHookHelper;
use App\Models\Client;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    public function install(Request $request): Response
    {
        $code = $request->input('code');
        if ($code === NULL) {
            return Response('Bad request', 400);
        }

        $response = AuthorizationHelper::getResponseForInstall($code);

        $oAuthAccessToken = ResponseHelper::getAccessToken($response);
        $eshopId = ResponseHelper::getEshopId($response);
        $eshopUrl = ResponseHelper::getFromResponse($response, 'eshopUrl');
        $contactEmail = ResponseHelper::getFromResponse($response, 'contactEmail');
        
        Client::updateOrCreate($eshopId, $oAuthAccessToken, $eshopUrl, $contactEmail);
        return Response('ok', 200);
    }

    public function deactivate(): Response
    {
        $eshopId = WebHookHelper::getEshopId(WebHookHelper::EVENT_DEACTIVATE);
        Client::updateStatus($eshopId, ClientStatusEnum::INACTIVE);

        return Response('ok', 200);
    }

    public function uninstall(): Response
    {
        $eshopId = WebHookHelper::getEshopId(WebHookHelper::EVENT_UNINSTALL);
        Client::updateStatus($eshopId, ClientStatusEnum::DELETED);

        return Response('ok', 200);
    }

    public function activate(): Response
    {
        $eshopId = WebHookHelper::getEshopId(WebHookHelper::EVENT_UNINSTALL);
        Client::updateStatus($eshopId, ClientStatusEnum::ACTIVE);
       
        return Response('ok', 200);
    }

    public function getApiAccessToken(Client $client): string
    {
        $apiAccessTokenUrl = env('SHOPTET_API_ACCESS_TOKEN_URL');

        $OauthAccessToken = $client->getAttribute('oauth_access_token');
        $curl = curl_init($apiAccessTokenUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $OauthAccessToken]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response, TRUE);
        if (ArrayHelper::containsKey($response, 'access_token') === false) {
            return Response('Bad request', 400);
        }
        return $response['access_token'];
    }

    public function settings(Request $request): View
    {
        $language = $request->input('language');
        $code = $request->input('code');
        $eshopId = $request->input('eshop_id');

        $client = Client::getByEshopId((int) $eshopId);
        $eshopResponse = ConnectorHelper::getEshop($client);
        $baseOAuthUrl = null;
        if ($eshopResponse->getOauthUrl() !== null) {
            $baseOAuthUrl = $eshopResponse->getOauthUrl();
            session(['base_oauth_url' => $baseOAuthUrl]);
        }
        if ($baseOAuthUrl === null) {
            $baseOAuthUrl = session('base_oauth_url');
        }
        if ($baseOAuthUrl === null) {
            throw new ApiRequestFailException(new Exception('Base OAuth URL not found in session or response for client ' . $client->getAttribute('eshop_id')));
        }

        $accessToken = AuthorizationHelper::getAccessTokenForSettings($code, $eshopId, $language, $baseOAuthUrl);
        $checkEshopId = AuthorizationHelper::getEshopId($accessToken, $baseOAuthUrl);
        LocaleHelper::setLocale($language);
        if ($checkEshopId !== $client->getAttribute('eshop_id')) {
            abort(404);
        }
        return view('settings',
            [
                'language' => $language,
                'code' => $code,
                'eshop_name' => $client->getAttribute('eshop_name'),
                'eshop_id' => $client->getAttribute('eshop_id'),
                'infinite_repeat' => $client->getAttribute('settings_infinite_repeat'),
                'return_to_default' => $client->getAttribute('settings_return_to_default'),
                'show_time' => $client->getAttribute('settings_show_time'),
            ]);
    }
}
