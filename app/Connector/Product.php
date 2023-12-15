<?php
declare(strict_types=1);

namespace App\Connector;

class Product
{
    public const METHOD = 'GET';
    public const ENDPOINT = '/products';
    public const QUERY = [];

    public static function getEndpoint(?string $guid = null): string
    {
        if ($guid !== null) {
            return self::ENDPOINT . '/' . $guid;
        }
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