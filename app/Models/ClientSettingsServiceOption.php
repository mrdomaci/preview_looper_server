<?php

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

    public function settingsService(): BelongsTo
    {
        return $this->belongsTo(SettingsService::class);
    }

    public function settingsServiceOption(): BelongsTo
    {
        return $this->belongsTo(SettingsServiceOption::class);
    }

    public static function updateOrCreate(Client $client, SettingsService $settingsService, SettingsServiceOption $settingsServiceOption): ClientSettingsServiceOption
    {
        $clientSettingsServiceOption = ClientSettingsServiceOption::where('client_id', $client->getAttribute('id'))->where('settings_service_id', $settingsService->getAttribute('id'))->first();
        if ($clientSettingsServiceOption === null) {
            $clientSettingsServiceOption = new ClientSettingsServiceOption();
            $clientSettingsServiceOption->setAttribute('client_id', $client->getAttribute('id'));
            $clientSettingsServiceOption->setAttribute('settings_service_id', $settingsService->getAttribute('id'));
        }
        $clientSettingsServiceOption->setAttribute('settings_service_option_id', $settingsServiceOption->getAttribute('id'));
        $clientSettingsServiceOption->save();
        return $clientSettingsServiceOption;
    }
}
