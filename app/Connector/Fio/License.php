<?php

declare(strict_types=1);

namespace App\Connector\Fio;

use DateTime;

class License
{
    public const METHOD = 'GET';
    public const ENDPOINT = '/%s/%s/%s/transactions.json';
    public const QUERY = [];

    public static function getEndpoint(DateTime $from, DateTime $to, string $currency): string
    {
        if ($currency === 'CZK') {
            return sprintf(self::ENDPOINT, env('FIO_API_KEY'), $from->format('Y-m-d'), $to->format('Y-m-d'));
        }
        if ($currency === 'EUR') {
            return sprintf(self::ENDPOINT, env('FIO_API_KEY_EUR'), $from->format('Y-m-d'), $to->format('Y-m-d'));
        }
        return 'unknown_currency_endpoint';
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
