<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Exceptions\RequestFailException;
use App\Models\Client;
use App\Models\Image;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class ResponseHelper
{
    private const CDN_URL = 'https://cdn.myshoptet.com/usr/%s.myshoptet.com/user/shop/detail/%s';
    /**
     * @param Collection<Image> $images
     * @param Client $client
     * @return array<int|string, array<int|string, mixed>>
     */
    public static function getImageResponseArray(Collection $images, Client $client): array
    {
        $response = ['settings' => ['infinite_repeat' => $client->getAttribute('settings_infinite_repeat'), 'return_to_default' => $client->getAttribute('settings_return_to_default'), 'show_time' => $client->getAttribute('settings_show_time')]];
        foreach($images as $image) {
            $product = $image->product;
            $response[$product->getAttribute('guid')][] = sprintf(self::CDN_URL, (string) $client->getAttribute('eshop_id'), $image->getAttribute('name'));
        }
        return $response;
    }

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
    public static function getEshopUrl(array $response): ?string
    {
        $eshopUrl = null;
        if (ArrayHelper::containsKey($response, 'eshopUrl')) {
            $eshopUrl = $response['eshopUrl'];
        }
        return $eshopUrl;
    }

    /**
     * @param array<string, string> $response
     * @return string
     */
    public static function getContactEmail(array $response): ?string
    {
        $contactEmail = null;
        if (ArrayHelper::containsKey($response, 'contactEmail')) {
            $contactEmail = $response['contactEmail'];
        }
        return $contactEmail;
    }
}