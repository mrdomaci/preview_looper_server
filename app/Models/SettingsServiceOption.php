<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Throwable;

class SettingsServiceOption extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'value',
        'settings_service_id',
        'is_default'
    ];

    public function getId(): int
    {
        return $this->getAttribute('id');
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function setName(string $name): self
    {
        return $this->setAttribute('name', $name);
    }

    public function getValue(): string
    {
        return $this->getAttribute('value');
    }

    public function setValue(string $value): self
    {
        return $this->setAttribute('value', $value);
    }

    public function getSettingsServiceId(): int
    {
        return $this->getAttribute('settings_service_id');
    }

    public function setSettingsService(SettingsService $settingsService): self
    {
        return $this->setAttribute('settings_service_id', $settingsService->getId());
    }

    public function isDefault(): bool
    {
        return (bool) $this->getAttribute('is_default');
    }

    public function setIsDefault(bool $isDefault): self
    {
        return $this->setAttribute('is_default', $isDefault);
    }

    public function settingsService(): BelongsTo
    {
        return $this->belongsTo(SettingsService::class);
    }

    public function isSelected(Client $client): bool
    {
        try {
            $clientSettingsServiceOption = ClientSettingsServiceOption::where('client_id', $client->getId())->where('settings_service_id', $this->getSettingsServiceId())->firstOrFail();
        } catch (Throwable) {
            if ($this->isdefault()) {
                return true;
            }
            return false;
        }
        if ($clientSettingsServiceOption->getSettingsServiceOptionId() === $this->getId()) {
            return true;
        }
        return false;
    }
}
