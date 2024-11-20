<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

class TemplateIncludeDelete
{
    public const METHOD = 'DELETE';
    public const ENDPOINT = '/template-include/common-header';

    public static function getEndpoint(): string
    {
        return self::ENDPOINT;
    }

    public static function getMethod(): string
    {
        return self::METHOD;
    }
}
