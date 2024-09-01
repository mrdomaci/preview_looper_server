<?php

declare(strict_types=1);

namespace App\Connector\Shoptet;

class QueueResponse
{
    public function __construct(
        private string $jobId,
    ) {
    }
    public function getJobId(): string
    {
        return $this->jobId;
    }
}
