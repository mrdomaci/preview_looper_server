<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

class Queue implements Endpoint
{
    public const METHOD = 'GET';
    public const ENDPOINT = '/system/jobs';
    public const QUERY = [];

    public static function getEndpoint(?string $jobId = null): string
    {
        if ($jobId !== null) {
            return self::ENDPOINT . '/' . $jobId;
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
