<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = null; 
    public $incrementing = false;

    protected $fillable = [
        'client_id',
        'guid',
        'code',
        'status',
        'paid',
        'foreignStatusId',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getClientId(): int
    {
        return $this->getAttribute('client_id');
    }

    public function setClient(Client $client): self
    {
        return $this->setAttribute('client_id', $client->getId());
    }

    public function getGuid(): string
    {
        return $this->getAttribute('guid');
    }

    public function setGuid(string $guid): self
    {
        return $this->setAttribute('guid', $guid);
    }

    public function getCode(): string
    {
        return $this->getAttribute('code');
    }

    public function setCode(string $code): self
    {
        return $this->setAttribute('code', $code);
    }

    public function isPaid(): bool
    {
        return (bool) $this->getAttribute('paid');
    }

    public function setPaid(bool $paid): self
    {
        return $this->setAttribute('paid', $paid);
    }

    public function getForeignStatusId(): string
    {
        return $this->getAttribute('foreign_status_id');
    }

    public function setForeignStatusId(string $foreignStatusId): self
    {
        return $this->setAttribute('foreign_status_id', $foreignStatusId);
    }
}
