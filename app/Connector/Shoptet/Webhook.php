<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

class Webhook
{
    public const METHOD = 'POST';
    public const ENDPOINT = '/webhooks';
    public string $body = '{
        "data": [
          {
            "event": "job:finished",
            "url": "https://slabihoud.cz/webhook/job"
          }
        ]
      }';

    public static function getEndpoint(): string
    {
        return self::ENDPOINT;
    }

    public static function getMethod(): string
    {
        return self::METHOD;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): Webhook
    {
        $this->body = $body;
        return $this;
    }
}
