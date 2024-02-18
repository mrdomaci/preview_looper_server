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

    public function getSettingsServiceId(): int
    {
        return $this->getAttribute('settings_service_id');
    }

    public function getSettingsServiceOptionId(): int
    {
        return $this->getAttribute('settings_service_option_id');
    }

    public function getValue(): string
    {
        return $this->getAttribute('value');
    }

    public function settingsService(): BelongsTo
    {
        return $this->belongsTo(SettingsService::class);
    }

    public function settingsServiceOption(): BelongsTo
    {
        return $this->belongsTo(SettingsServiceOption::class);
    }

    public static function updateOrCreate(Client $client, SettingsService $settingsService, ?SettingsServiceOption $settingsServiceOption, ?string $value): ClientSettingsServiceOption
    {
        $clientSettingsServiceOption = ClientSettingsServiceOption::where('client_id', $client->getId())->where('settings_service_id', $settingsService->getId())->first();
        if ($clientSettingsServiceOption === null) {
            $clientSettingsServiceOption = new ClientSettingsServiceOption();
            $clientSettingsServiceOption->setAttribute('client_id', $client->getId());
            $clientSettingsServiceOption->setAttribute('settings_service_id', $settingsService->getId());
        }
        if ($settingsServiceOption !== null) {
            if ($settingsServiceOption->getAttribute('id') !== null) {
                $settingsServiceOption = $settingsServiceOption->getId();
            } else {
                $settingsServiceOption = $settingsServiceOption->getSettingsServiceId();
            }
        }

        $clientSettingsServiceOption->setAttribute('settings_service_option_id', $settingsServiceOption);
        $clientSettingsServiceOption->setAttribute('value', $value);
        $clientSettingsServiceOption->save();
        return $clientSettingsServiceOption;
    }
}
