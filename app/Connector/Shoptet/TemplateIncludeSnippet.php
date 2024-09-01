<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

class TemplateIncludeSnippet
{
    public function __construct(
        private string $location,
        private string $html,
    ) {
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function getHtml(): string
    {
        return $this->html;
    }
}
