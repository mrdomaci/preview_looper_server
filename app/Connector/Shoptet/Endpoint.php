<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

interface Endpoint
{
    public const METHOD = 'GET';
    public const ENDPOINT = '/products/availabilities';
    public const QUERY = [];

    public static function getEndpoint(): string;

    /**
     * @return array<string, string>
     */
    public static function getQuery(): array;

    public static function getMethod(): string;
}
