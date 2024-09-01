<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

class TemplateInclude
{
    public const METHOD = 'POST';
    public const ENDPOINT = '/template-include';
    public string $body = '{
        "data": {
          "snippets": [
            {
              "location": "common-header",
              "html": "<div id=\'preview-looper-settings\' data-infinite-repeat=\'0\' data-return-to-default=\'1\' data-show-time=\'1000\'></div>"
            }
          ]
        }
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

    public function setBody(string $body): TemplateInclude
    {
        $this->body = $body;
        return $this;
    }
}
