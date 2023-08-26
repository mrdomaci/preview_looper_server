<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Exceptions\RequestFailException;
use Exception;
use Illuminate\Support\Facades\Http;

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

    public static function findTemplateName(?string $javascriptCode): ?string
    {
        if ($javascriptCode === null) {
            return null;
        }
        preg_match('/shoptet\.design = \{"template":\{"name":"(.*?)",/', $javascriptCode, $matches);
        return isset($matches[1]) ? $matches[1] : null;
    }

    public static function getUrlResponse(string $url): ?string
    {
        try {
            $response = Http::get($url);
            return $response->body();
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function extractShoptetScriptFromBody(?string $htmlString): ?string
    {
        if ($htmlString === null) {
            return null;
        }
        $startTag = '<body';
        $endTag = '</body>';
        $scriptTagStart = '<script>var shoptet = shoptet || {};shoptet.abilities = ';
        
        $startPos = strpos($htmlString, $startTag);
        $endPos = strpos($htmlString, $endTag, $startPos);
        if ($startPos === false || $endPos === false) {
            return null;
        }
    
        $bodyContent = substr($htmlString, $startPos + strlen($startTag), $endPos - $startPos - strlen($startTag));
    
        $scriptTagStartPos = strpos($bodyContent, $scriptTagStart);
        $scriptTagEndPos = strpos($bodyContent, '</script>');
    
        if ($scriptTagStartPos !== false && $scriptTagEndPos !== false) {
            return substr($bodyContent, $scriptTagStartPos + strlen($scriptTagStart), $scriptTagEndPos - $scriptTagStartPos - strlen($scriptTagStart));
        }
        
        return null; // shoptet script not found
    }    
}