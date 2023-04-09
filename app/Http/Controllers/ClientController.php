<?php

namespace App\Http\Controllers;

use App\Enums\ClientStatusEnum;
use App\Exceptions\DataInsertFailException;
use App\Exceptions\DataUpdateFailException;
use App\Helpers\ArrayHelper;
use App\Helpers\JsonHelper;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Nette\Utils\Json;
use Throwable;

class ClientController extends Controller
{
    private const EVENT_UNINSTALL = 'addon:uninstall';
    private const EVENT_DEACTIVATE = 'addon:deactivate';

    public function install(Request $request): Response
    {
        $code = $request->input('code');
        if ($code === NULL) {
            return Response('Bad request', 400);
        }
        $clientId = env('SHOPTET_CLIENT_ID');
        $clientSecret = env('SHOPTET_CLIENT_SECRET');
        $oAuthServerTokenUrl = env('SHOPTET_OAUTH_SERVER_TOKEN_URL');
        $grantType = 'authorization_code';
        $scope = 'api';
        $redirectUri = Route('client.install');

        $data = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret, 
            'code' => $code,
            'grant_type' => $grantType,
            'redirect_uri' => $redirectUri,
            'scope' => $scope,
        ];

        $curl = curl_init($oAuthServerTokenUrl);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, TRUE);
        if (ArrayHelper::containsKey($response, 'access_token') === false) {
            return Response('Bad request', 400);
        }
        $oAuthAccessToken = $response['access_token'];

        if (ArrayHelper::containsKey($response, 'eshop_id') === false) {
            return Response('Bad request', 400);
        }
        $eshopId = $response['eshop_id'];

        $eshopUrl = NULL;
        if (ArrayHelper::containsKey($response, 'eshopUrl')) {
            $eshopUrl = $response['eshopUrl'];
        }
        $contactEmail = NULL;
        if (ArrayHelper::containsKey($response, 'contactEmail')) {
            $contactEmail = $response['contactEmail'];
        }

        $client = Client::where('eshop_id', $eshopId)->first();
        if ($client === NULL) {
            try {
                Client::create([
                    'oauth_access_token' => $oAuthAccessToken,
                    'eshop_id' => $eshopId,
                    'eshop_url' => $eshopUrl,
                    'contact_email' => $contactEmail,
                    'status' => ClientStatusEnum::ACTIVE,
                ]);
            } catch (Throwable $t) {
                throw new DataInsertFailException($t);
            }
        } else {
            $client->oauth_access_token = $oAuthAccessToken;
            $client->eshop_url = $eshopUrl;
            $client->contact_email = $contactEmail;
            $client->status = ClientStatusEnum::ACTIVE;
            try {
                $client->save();
            } catch (Throwable $t) {
                throw new DataUpdateFailException($t);
            }
        }
        return Response('ok', 200);
    }

    public function deactivate(): Response
    {
        $body = file_get_contents('php://input');
        $webhook = json_decode($body, TRUE);
        $eshopId = $webhook['eshopId'];
        $event = $webhook['event'];
        if ($event !== self::EVENT_DEACTIVATE) {
            return Response('bad request', 400);
        }
        $client = Client::where('eshop_id', $eshopId)->firstOrFail();
        $client->status = ClientStatusEnum::INACTIVE;
        try {
            $client->save();
        } catch (Throwable $t) {
            throw new DataUpdateFailException($t);
        }
        return Response('ok', 200);
    }

    public function uninstall(): Response
    {
        $body = file_get_contents('php://input');
        $webhook = Json::decode($body);
        if (JsonHelper::containsKey($webhook, 'event') === false) {
            return Response('bad request', 400);
        }
        $event = $webhook->event;
        if ($event !== self::EVENT_UNINSTALL) {
            return Response('bad request', 400);
        }
        if (JsonHelper::containsKey($webhook, 'eshopId') === false) {
            return Response('bad request', 400);
        }
        $eshopId = $webhook->eshopId;
       
        $client = Client::where('eshop_id', $eshopId)->firstOrFail();
        $client->status = ClientStatusEnum::DELETED;
        try {
            $client->save();
        } catch (Throwable $t) {
            throw new DataUpdateFailException($t);
        }
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
}
