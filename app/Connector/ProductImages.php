<?php
declare(strict_types=1);

namespace App\Connector;

class ProductImages
{
    public const METHOD = 'GET';
    public const ENDPOINT = '/products/%s/images/%s';
    public const QUERY = [];

    public static function getEndpoint(string $productGuid, string $gallery): string
    {
        return sprintf(self::ENDPOINT, $productGuid, $gallery);
    }

    /**
     * @return array<string, string>
     */
    public static function getQuery(): array
    {
        return self::QUERY;
    }

    public static function getMethod(): string
    {
        return self::METHOD;
    }
}