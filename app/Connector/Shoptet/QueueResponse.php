<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

use App\Enums\QueueStatusEnum;
use DateTime;

class QueueResponse
{
    public function __construct(
        private string $jobId,
        private ?string $endpoint = null,
        private ?DateTime $creationTime = null,
        private ?string $duration = null,
        private ?DateTime $completionTime = null,
        private ?QueueStatusEnum $status = null,
        private ?string $resultUrl = null,
        private ?DateTime $validUntil = null,
        private ?string $log = null,
    ) {
    }
    public function getJobId(): string
    {
        return $this->jobId;
    }

    public function getEndpoint(): ?string
    {
        return $this->endpoint;
    }

    public function getCreationTime(): ?DateTime
    {
        return $this->creationTime;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function getCompletionTime(): ?DateTime
    {
        return $this->completionTime;
    }

    public function getStatus(): ?QueueStatusEnum
    {
        return $this->status;
    }

    public function getResultUrl(): ?string
    {
        return $this->resultUrl;
    }

    public function getValidUntil(): ?DateTime
    {
        return $this->validUntil;
    }

    public function getLog(): ?string
    {
        return $this->log;
    }
}
