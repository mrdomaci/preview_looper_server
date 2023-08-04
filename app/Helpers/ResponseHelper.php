<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Exceptions\RequestFailException;
use Exception;

class ResponseHelper
{
    public const MAXIMUM_ITERATIONS = 10000;
    public const MAXIMUM_ITEMS_PER_PAGE = 20;
    private const CDN_URL = 'https://cdn.myshoptet.com/usr/%s/user/shop/detail/%s';

    /**
     * @param array<string, string> $response
     * @return string
     */
    public static function getAccessToken(array $response): string
    {
        if (ArrayHelper::containsKey($response, 'access_token') === false) {
            throw new RequestFailException(new Exception('Access token not found in response'));
        }
        return $response['access_token'];
    }

    /**
     * @param array<string, string> $response
     * @return int
     */
    public static function getEshopId(array $response): int
    {
        if (ArrayHelper::containsKey($response, 'eshopId') === false) {
            throw new RequestFailException(new Exception('Eshop ID not found in response'));
        }
        return (int) $response['eshopId'];
    }

    /**
     * @param array<string, string> $response
     * @return string
     */
    public static function getFromResponse(array $response, string $key): ?string
    {
        $value = null;
        if (ArrayHelper::containsKey($response, $key)) {
            $value = $response[$key];
        }
        return $value;
    }

    public static function getUImageURL(string $eshopName, string $imageName): string
    {
        return sprintf(self::CDN_URL, $eshopName, $imageName);
    }
}