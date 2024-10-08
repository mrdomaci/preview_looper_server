<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

class Availability implements Endpoint
{
    public const METHOD = 'GET';
    public const ENDPOINT = '/products/availabilities';
    public const QUERY = [];

    public static function getEndpoint(): string
    {
        return self::ENDPOINT;
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
