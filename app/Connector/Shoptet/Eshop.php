<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

class Eshop implements Endpoint
{
    public const METHOD = 'GET';
    public const ENDPOINT = '/eshop';
    public const QUERY = [];

    public static function getEndpoint(): string
    {
        return self::ENDPOINT;
    }

    public static function getMethod(): string
    {
        return self::METHOD;
    }

    public static function getQuery(): array
    {
        return self::QUERY;
    }
}
