<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ClientServiceQueueStatusEnum;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientServiceQueue extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'status',
        'client_service_id',
    ];

    public function clientService(): BelongsTo
    {
        return $this->belongsTo(ClientService::class);
    }

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getClientServiceId(): int
    {
        return $this->getAttribute('client_service_id');
    }

    public function getStatus(): ClientServiceQueueStatusEnum
    {
        return ClientServiceQueueStatusEnum::fromCase($this->getAttribute('status'));
    }

    public function next(): void
    {
        $clientService = $this->clientService()->first();
        $clientServiceStatus = $this->getStatus();
        $this->status = $clientServiceStatus->next($clientService->service()->first())->name;
        $this->save();
        if ($this->getStatus()->isDone()) {
            $clientService->setSyncedAt(new DateTime());
            $clientService->save();
        }
    }
}
