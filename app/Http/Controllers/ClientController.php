<?php

namespace App\Http\Controllers;

use App\Enums\ClientStatusEnum;
use App\Helpers\ArrayHelper;
use App\Helpers\AuthorizationHelper;
use App\Helpers\LocaleHelper;
use App\Helpers\NumbersHelper;
use App\Helpers\ResponseHelper;
use App\Helpers\WebHookHelper;
use App\Models\Client;
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
        $eshopUrl = ResponseHelper::getEshopUrl($response);
        $contactEmail = ResponseHelper::getContactEmail($response);
        
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

    public function settings(string $language, string $code, Request $request): View
    {
        $baseOAuthUrl = $request->session()->get('base_oauth_url');
        $accessToken = AuthorizationHelper::getAccessTokenForSettings($code, $language, $baseOAuthUrl);
        $eshopId = AuthorizationHelper::getEshopId($accessToken, $baseOAuthUrl);
        LocaleHelper::setLocale($language);
        $client = Client::where('eshop_id', (int) $eshopId)->first();
        if ($client === NULL) {
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

    public function saveSettings(string $language, string $code, Request $request): \Illuminate\Http\RedirectResponse
    {
        LocaleHelper::setLocale($language);
        $infiniteRepeat = $request->input('settings_infinite_repeat');
        $returnToDefault = $request->input('settings_return_to_default');
        $showTime = $request->input('settings_show_time');
        $eshopId = $request->input('eshop_id');
        $client = Client::where('eshop_id', (int) $eshopId)->first();
        if ($client === NULL) {
            abort(404);
        }
        Client::updateSettings($client, NumbersHelper::intToBool((int)$infiniteRepeat), NumbersHelper::intToBool((int)$returnToDefault), (int)$showTime);
        return redirect()->route('client.settings', ['language' => $language, 'code' => $code])->with('success', trans('messages.saved'));
    }
}
