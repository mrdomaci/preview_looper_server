<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientSettingsServiceOption extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'settings_service_id',
        'settings_service_option_id',
        'client_id',
        'value',
    ];

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getClientId(): int
    {
        return $this->getAttribute('client_id');
    }

    public function setClient(Client $client): self
    {
        return $this->setAttribute('client_id', $client->getId());
    }

    public function getSettingsServiceId(): int
    {
        return $this->getAttribute('settings_service_id');
    }

    public function setSettingsService(SettingsService $settingsService): self
    {
        return $this->setAttribute('settings_service_id', $settingsService->getId());
    }

    public function getSettingsServiceOptionId(): ?int
    {
        return $this->getAttribute('settings_service_option_id');
    }

    public function setSettingsServiceOption(?SettingsServiceOption $settingsServiceOption): self
    {
        return $this->setAttribute('settings_service_option_id', $settingsServiceOption->getId());
    }

    public function getValue(): ?string
    {
        return $this->getAttribute('value');
    }

    public function setValue(?string $value): self
    {
        return $this->setAttribute('value', $value);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function settingsService(): BelongsTo
    {
        return $this->belongsTo(SettingsService::class);
    }

    public function settingsServiceOption(): BelongsTo
    {
        return $this->belongsTo(SettingsServiceOption::class);
    }
}
