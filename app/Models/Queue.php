<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QueueStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'endpoint',
        'status',
        'reqsult_url',
        'client_service_id'
    ];

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getJobId(): string
    {
        return $this->getAttribute('job_id');
    }

    public function setJobId(string $jobId): self
    {
        return $this->setAttribute('job_id', $jobId);
    }

    public function getEndpoint(): string
    {
        return $this->getAttribute('endpoint');
    }

    public function setEndpoint(string $endpoint): self
    {
        return $this->setAttribute('endpoint', $endpoint);
    }

    public function getStatus(): QueueStatusEnum
    {
        return QueueStatusEnum::fromCase($this->getAttribute('status'));
    }

    public function setStatus(QueueStatusEnum $status): self
    {
        return $this->setAttribute('status', $status->name);
    }

    public function getResultUrl(): string
    {
        return $this->getAttribute('reqsult_url');
    }

    public function setResultUrl(string $resultUrl): self
    {
        return $this->setAttribute('reqsult_url', $resultUrl);
    }

    public function getClientServiceId(): int
    {
        return $this->getAttribute('client_service_id');
    }

    public function setClientServiceId(int $clientServiceId): self
    {
        return $this->setAttribute('client_service_id', $clientServiceId);
    }
}
