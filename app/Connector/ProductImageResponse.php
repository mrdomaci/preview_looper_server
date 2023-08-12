<?php
declare(strict_types=1);

namespace App\Connector;

use DateTime;

class ProductImageResponse
{
    public function __construct(
        private string $name,
        private ?int $priority,
        private string $seoName,
        private string $cdnName,
        private ?string $description,
        private ?DateTime $changeTime,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function getSeoName(): string
    {
        return $this->seoName;
    }

    public function getCdnName(): string
    {
        return $this->cdnName;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getChangeTime(): ?DateTime
    {
        return $this->changeTime;
    }
}