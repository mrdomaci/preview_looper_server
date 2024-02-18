<?php

declare(strict_types=1);

namespace App\Connector;

class ProductImages
{
    public const METHOD = 'GET';
    public const ENDPOINT = '/products/%s/images/%s';

    public static function getEndpoint(string $productGuid, string $gallery): string
    {
        return sprintf(self::ENDPOINT, $productGuid, $gallery);
    }

    public static function getMethod(): string
    {
        return self::METHOD;
    }
}
