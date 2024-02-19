<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Client;
use App\Models\Service;
use Illuminate\Http\Request;

class SessionHelper
{
    public static function hasAccessToken(Request $request, Client $client, Service $service): bool
    {
        return $request->session()->has($client->getEshopId() . '_' . $service->getId() . '_access_token');
    }

    public static function hasBaseOauthUrl(Request $request, Client $client, Service $service): bool
    {
        return $request->session()->has($client->getEshopId() . '_' . $service->getId() . '_base_oauth_url');
    }

    public static function getAccessToken(Request $request, Service $service, Client $client): string
    {
        return $request->session()->get($client->getEshopId() . '_' . $service->getId() . '_access_token');
    }

    public static function getBaseOauthUrl(Request $request, Service $service, Client $client): string
    {
        return $request->session()->get($client->getEshopId() . '_' . $service->getId() . '_base_oauth_url');
    }

    public static function setAccessToken(Request $request, Client $client, Service $service, string $accessToken): void
    {
        $request->session()->put($client->getEshopId() . '_' . $service->getId() . '_access_token', $accessToken);
    }

    public static function setBaseOauthUrl(Request $request, Client $client, Service $service, string $baseOauthUrl): void
    {
        $request->session()->put($client->getEshopId() . '_' . $service->getId() . '_base_oauth_url', $baseOauthUrl);
    }
}
