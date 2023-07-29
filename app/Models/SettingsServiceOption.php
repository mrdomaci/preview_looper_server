<?php

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
    ];

    public function settingsService(): BelongsTo
    {
        return $this->belongsTo(SettingsService::class);
    }

    public function isSelected(Client $client): bool
    {
        $clientSettingsServiceOption = ClientSettingsServiceOption::where('client_id', $client->getAttribute('id'))->where('settings_service_id', $this->getAttribute('settings_service_id'))->first();
        if ($clientSettingsServiceOption === null && $this->getAttribute('is_default') === 1) {
            return true;
        } else if ($clientSettingsServiceOption !== null && $clientSettingsServiceOption->getAttribute('settings_service_option_id') === $this->getAttribute('id')) {
            return true;
        }
        return false;
    }
}
