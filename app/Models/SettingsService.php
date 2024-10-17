<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Throwable;

class SettingsService extends Model
{
    use HasFactory;

    public const UPSELL_MAX_RESULTS = 7;
    public const UPSELL_HEADER = 8;
    public const UPSELL_COMPANY_NAME = 9;
    public const UPSELL_CIN = 10;
    public const UPSELL_TIN = 11;
    public const UPSELL_COMPANY_ADDRESS = 12;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'sort',
        'service_id',
        'type',
        'is_default',
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

    public function getSort(): int
    {
        return $this->getAttribute('sort');
    }

    public function setSort(int $sort): self
    {
        return $this->setAttribute('sort', $sort);
    }

    public function getServiceId(): int
    {
        return $this->getAttribute('service_id');
    }

    public function setService(Service $service): self
    {
        return $this->setAttribute('service_id', $service->getId());
    }

    public function getType(): string
    {
        return $this->getAttribute('type');
    }

    public function setType(string $type): self
    {
        return $this->setAttribute('type', $type);
    }

    public function isDefault(): bool
    {
        return (bool) $this->getAttribute('is_default');
    }

    public function setIsDefault(bool $isDefault): self
    {
        return $this->setAttribute('is_default', $isDefault);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function settingsServicesOptions(): HasMany
    {
        return $this->hasMany(SettingsServiceOption::class);
    }

    public function clientSettingsServiceOptions(): HasMany
    {
        return $this->hasMany(ClientSettingsServiceOption::class);
    }

    public function getValue(Client $client): ?string
    {
        try {
            $clientSettingsServiceOption = ClientSettingsServiceOption::where('client_id', $client->getId())->where('settings_service_id', $this->getAttribute('id'))->firstOrFail();
            return $clientSettingsServiceOption->getAttribute('value');
        } catch (Throwable) {
            return null;
        }
    }
}
