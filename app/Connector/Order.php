<?php
declare(strict_types=1);

namespace App\Connector;

use App\Helpers\DateTimeHelper;
use DateTime;

class Order
{
    public const METHOD = 'GET';
    public const ENDPOINT = '/orders';
    public const QUERY = [];

    public static function getEndpoint(?DateTime $changeFrom = null): string
    {
        if ($changeFrom !== null) {
            $filter = DateTimeHelper::getDateTimeString($changeFrom);
            return self::ENDPOINT . '/?changeTimeFrom=' . $filter;
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