<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SettingsService extends Model
{
    use HasFactory;

    public const UPSELL_MAX_RESULTS = 7;

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

    public function getSort(): int
    {
        return $this->getAttribute('sort');
    }

    public function getServiceId(): int
    {
        return $this->getAttribute('service_id');
    }

    public function getType(): string
    {
        return $this->getAttribute('type');
    }

    public function isDefault(): bool
    {
        return (bool) $this->getAttribute('is_default');
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
        $clientSettingsServiceOption = ClientSettingsServiceOption::where('client_id', $client->getId())->where('settings_service_id', $this->getAttribute('id'))->first();
        if ($clientSettingsServiceOption === null) {
            return null;
        }
        return $clientSettingsServiceOption->getAttribute('value');
    }
}
