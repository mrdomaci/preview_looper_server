<?php
declare(strict_types=1);

namespace App\Connector;

class OrderDetail
{
    public const METHOD = 'GET';
    public const ENDPOINT = '/orders/%s';
    public const QUERY = [];

    public static function getEndpoint(string $code): string
    {
        return sprintf(self::ENDPOINT, $code);
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