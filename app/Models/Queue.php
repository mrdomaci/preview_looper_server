<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\QueueStatusEnum;
use App\Enums\QueueTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    use HasFactory;

    public $incrementing = false;
    public $timestamps = true;
    protected $keyType = 'string'; 

    protected $fillable = [
        'job_id',
        'endpoint',
        'status',
        'result_url',
        'client_service_id',
        'type',
    ];

    public function getKeyName()
    {
        return ['job_id', 'client_service_id', 'type'];
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

    public function setStatus(string $status): self
    {
        return $this->setAttribute('status', $status);
    }

    public function getResultUrl(): string
    {
        return $this->getAttribute('result_url');
    }

    public function setResultUrl(?string $resultUrl): self
    {
        return $this->setAttribute('result_url', $resultUrl);
    }

    public function getClientServiceId(): int
    {
        return $this->getAttribute('client_service_id');
    }

    public function setClientServiceId(int $clientServiceId): self
    {
        return $this->setAttribute('client_service_id', $clientServiceId);
    }

    public function clientService(): BelongsTo
    {
        return $this->belongsTo(ClientService::class);
    }

    public function getType(): QueueTypeEnum
    {
        return QueueTypeEnum::fromCase($this->getAttribute('type'));
    }

    public function setType(string $type): self
    {
        return $this->setAttribute('type', $type);
    }
}
