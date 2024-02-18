<?php

declare(strict_types=1);

namespace App\Connector;

class TemplateIncludeResponse
{
    /**
     * @param array<TemplateIncludeSnippet> $templateIncludes
     */
    public function __construct(
        private array $templateIncludes,
    ) {
    }
    /**
     * @return array<TemplateIncludeSnippet>
     */
    public function getTemplateIncludes(): array
    {
        return $this->templateIncludes;
    }
}
