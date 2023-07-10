<?php

namespace App\Http\Controllers;

use App\Enums\ClientStatusEnum;
use App\Exceptions\ApiRequestFailException;
use App\Helpers\ArrayHelper;
use App\Helpers\AuthorizationHelper;
use App\Helpers\ConnectorBodyHelper;
use App\Helpers\ConnectorHelper;
use App\Helpers\LocaleHelper;
use App\Helpers\LoggerHelper;
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
        
        $client = Client::updateOrCreate($eshopId, $oAuthAccessToken, $eshopUrl, $contactEmail);

        $webhookResponse = WebHookHelper::jenkinsWebhook($client->getAttribute('id'));
        if ($webhookResponse->failed()) {
            LoggerHelper::log('Webhook failed: ' . $webhookResponse->body() . ', Status code: ' . $webhookResponse->status());
        }
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
        $eshopId = $request->input('eshop_id');

        $client = Client::getByEshopId((int) $eshopId);
        if ($request->session()->has('access_token') === false) {
            $code = $request->input('code');
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
            $request->session()->put('access_token', $accessToken);   
            $request->session()->put('base_oauth_url', $baseOAuthUrl);
        } else {
            $accessToken = $request->session()->get('access_token');
            $baseOAuthUrl = $request->session()->get('base_oauth_url');
        }

        $checkEshopId = AuthorizationHelper::getEshopId($accessToken, $baseOAuthUrl);
        LocaleHelper::setLocale($language);
        if ($checkEshopId !== $client->getAttribute('eshop_id')) {
            abort(404);
        }
        return view('settings',
            [
                'language' => $language,
                'eshop_id' => $client->getAttribute('eshop_id'),
                'infinite_repeat' => $client->getAttribute('settings_infinite_repeat'),
                'return_to_default' => $client->getAttribute('settings_return_to_default'),
                'show_time' => $client->getAttribute('settings_show_time'),
                'footer_link' => 'layouts.terms_link'
            ]);
    }

    public function saveSettings(string $language, string $eshopId, Request $request): \Illuminate\Http\RedirectResponse
    {
        LocaleHelper::setLocale($language);
        $infiniteRepeat = $request->input('settings_infinite_repeat');
        $returnToDefault = $request->input('settings_return_to_default');
        $showTime = $request->input('settings_show_time');
        if ($eshopId !== $request->input('eshop_id')) {
            abort(403);
        }
        $client = Client::getByEshopId((int) $eshopId);
        $infiniteRepeat = NumbersHelper::intToBool((int)$infiniteRepeat);
        $returnToDefault = NumbersHelper::intToBool((int)$returnToDefault);
        $showTime = (int)$showTime;
        Client::updateSettings($client, $infiniteRepeat, $returnToDefault, $showTime);
        $body = ConnectorBodyHelper::getStringBodyForTemplateInclude($infiniteRepeat, $returnToDefault, $showTime);
        $templateIncludeResponse = ConnectorHelper::postTemplateInclude($client, $body);
        if ($templateIncludeResponse->getTemplateIncludes() === []) {
            LoggerHelper::log('Template include failed for client ' . $client->getAttribute('eshop_id'));
            return redirect()->route('client.settings', ['language' => $language, 'eshop_id' => $eshopId])->with('error', trans('messages.error'));
        }
        return redirect()->route('client.settings', ['language' => $language, 'eshop_id' => $eshopId])->with('success', trans('messages.saved'));
    }
}
