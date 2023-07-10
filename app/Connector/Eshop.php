<?php
declare(strict_types=1);

namespace App\Connector;

class Eshop
{
    public const METHOD = 'GET';
    public const ENDPOINT = '/eshop';

    public static function getEndpoint(): string
    {
        return self::ENDPOINT;
    }

    public static function getMethod(): string
    {
        return self::METHOD;
    }
}