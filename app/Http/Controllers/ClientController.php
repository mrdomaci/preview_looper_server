<?php

namespace App\Http\Controllers;

use App\Enums\ClientStatusEnum;
use App\Exceptions\AddonInstallFailException;
use App\Exceptions\DataInsertFailException;
use App\Exceptions\DataUpdateFailException;
use App\Helpers\ArrayHelper;
use App\Helpers\JsonHelper;
use App\Models\Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Nette\Utils\Json;
use Throwable;

class ClientController extends Controller
{
    private const EVENT_UNINSTALL = 'addon:uninstall';
    private const EVENT_DEACTIVATE = 'addon:suspend';
    private const EVENT_ACTIVATE = 'addon:approve';

    public function install(Request $request): Response
    {
        $code = $request->input('code');
        if ($code === NULL) {
            return Response('Bad request', 400);
        }

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
        if (JsonHelper::containsKey($response, 'error') === false) {
            throw new AddonInstallFailException(new Exception($response->error . ': ' . $response->error_description));
        }
        if (JsonHelper::containsKey($response, 'access_token') === false) {
            return Response('Bad request', 400);
        }
        $oAuthAccessToken = $response->access_token;

        if (JsonHelper::containsKey($response, 'eshop_id') === false) {
            return Response('Bad request', 400);
        }
        $eshopId = $response->eshop_id;

        $eshopUrl = NULL;
        if (JsonHelper::containsKey($response, 'eshopUrl')) {
            $eshopUrl = $response->eshopUrl;
        }
        $contactEmail = NULL;
        if (JsonHelper::containsKey($response, 'contactEmail')) {
            $contactEmail = $response->contactEmail;
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
        $webhook = Json::decode($body);
        if (JsonHelper::containsKey($webhook, 'event') === false) {
            return Response('bad request', 400);
        }
        $event = $webhook->event;
        if ($event !== self::EVENT_DEACTIVATE) {
            return Response('bad request', 400);
        }
        if (JsonHelper::containsKey($webhook, 'eshopId') === false) {
            return Response('bad request', 400);
        }
        $eshopId = $webhook->eshopId;

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

    public function activate(): Response
    {
        $body = file_get_contents('php://input');
        $webhook = Json::decode($body);
        if (JsonHelper::containsKey($webhook, 'event') === false) {
            return Response('bad request', 400);
        }
        $event = $webhook->event;
        if ($event !== self::EVENT_ACTIVATE) {
            return Response('bad request', 400);
        }
        if (JsonHelper::containsKey($webhook, 'eshopId') === false) {
            return Response('bad request', 400);
        }
        $eshopId = $webhook->eshopId;
       
        $client = Client::where('eshop_id', $eshopId)->firstOrFail();
        $client->status = ClientStatusEnum::ACTIVE;
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
