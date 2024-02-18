<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function getValue(): string
    {
        return $this->getAttribute('value');
    }

    public function getSettingsServiceId(): int
    {
        return $this->getAttribute('settings_service_id');
    }

    public function isDefault(): bool
    {
        return (bool) $this->getAttribute('is_default');
    }

    public function settingsService(): BelongsTo
    {
        return $this->belongsTo(SettingsService::class);
    }

    public function isSelected(Client $client): bool
    {
        $clientSettingsServiceOption = ClientSettingsServiceOption::where('client_id', $client->getId())->where('settings_service_id', $this->getSettingsServiceId())->first();
        if ($clientSettingsServiceOption === null && $this->isdefault()) {
            return true;
        } else if ($clientSettingsServiceOption !== null && $clientSettingsServiceOption->getSettingsServiceOptionId() === $this->getId()) {
            return true;
        }
        return false;
    }
}
