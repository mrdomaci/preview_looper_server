<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Client;
use App\Models\ClientSettingsServiceOption;
use App\Models\SettingsService;
use App\Models\SettingsServiceOption;
use Throwable;

class ClientSettingsServiceOptionRepository
{

    /**
     * @param Client $client
     * @return int
     */
    public function getMaxResultsForUpsell(Client $client): int
    {
        try {
            return (int) ClientSettingsServiceOption::from('client_settings_service_options as csso')
                ->where('csso.client_id', $client->getId())
                ->join('settings_service_options as sso', 'sso.id', '=', 'csso.settings_service_option_id')
                ->where('csso.settings_service_id', SettingsService::UPSELL_MAX_RESULTS)
                ->pluck('sso.value')
                ->firstOrFail();
        } catch (Throwable) {
            return (int) SettingsServiceOption::where('settings_service_id', SettingsService::UPSELL_MAX_RESULTS)
                ->where('is_default', true)
                ->pluck('value')
                ->firstOrFail();
        }
    }

    public function updateOrCreate(Client $client, SettingsService $settingsService, ?SettingsServiceOption $settingsServiceOption, ?string $value): ClientSettingsServiceOption
    {
        try {
            $clientSettingsServiceOption = ClientSettingsServiceOption::where('client_id', $client->getId())->where('settings_service_id', $settingsService->getId())->firstOrFail();
        } catch (Throwable) {
            $clientSettingsServiceOption = new ClientSettingsServiceOption();
            $clientSettingsServiceOption->setAttribute('client_id', $client->getId());
            $clientSettingsServiceOption->setAttribute('settings_service_id', $settingsService->getId());
        }

        if ($settingsServiceOption->getAttribute('id') !== null) {
            $settingsServiceOption = $settingsServiceOption->getId();
        } else {
            $settingsServiceOption = $settingsServiceOption->getSettingsServiceId();
        }

        $clientSettingsServiceOption->setAttribute('settings_service_option_id', $settingsServiceOption);
        $clientSettingsServiceOption->setAttribute('value', $value);
        $clientSettingsServiceOption->save();
        return $clientSettingsServiceOption;
    }
}
