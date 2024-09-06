<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

use App\Enums\QueueStatusEnum;
use DateTime;

class JobResponse
{
    public function __construct(
        private string $jobId,
        private string $endpoint,
        private ?DateTime $creationTime,
        private ?DateTime $completionTime,
        private QueueStatusEnum $status,
        private ?DateTime $validUntil,
        private ?string $resultUrl,
    ) {
    }

    public function getJobId(): string
    {
        return $this->jobId;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getCreationTime(): ?DateTime
    {
        return $this->creationTime;
    }

    public function getCompletionTime(): ?DateTime
    {
        return $this->completionTime;
    }

    public function getStatus(): QueueStatusEnum
    {
        return $this->status;
    }

    public function getValidUntil(): ?DateTime
    {
        return $this->validUntil;
    }

    public function getResultUrl(): ?string
    {
        return $this->resultUrl;
    }
}
