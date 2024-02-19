<?php

declare(strict_types=1);

namespace App\Businesses;

use App\Models\Client;
use App\Models\ClientSettingsServiceOption;
use App\Models\Service;
use App\Models\SettingsServiceOption;
use App\Repositories\SettingsServiceOptionRepository;
use Illuminate\Http\Request;
use Throwable;

class SettingServiceBusiness
{
    public function __construct(
        private readonly SettingsServiceOptionRepository $settingsServiceOptionRepository
    ) {
    }
    public function updateOrCreateFromRequest(Request $request, Service $service, Client $client): void
    {
        foreach ($service->settingsServices()->get() as $settingsService) {
            $value = null;
            $selectedOption = $request->input((string) $settingsService->getId());
            try {
                $settingsServiceOption = $this->settingsServiceOptionRepository->get($selectedOption);
            } catch (Throwable) {
                if ($selectedOption !== '-') {
                    $selectedOption = (int) $selectedOption;
                } else {
                    $selectedOption = null;
                }
                $settingsServiceOption = new SettingsServiceOption(
                    [
                        'name' => 'default',
                        'value' => null,
                        'settings_service_id' => $selectedOption,
                    ]
                );
            }

            if ($request->input($settingsService->getId() . '_value') !== null) {
                $value = $request->input($settingsService->getId() . '_value');
            }
            ClientSettingsServiceOption::updateOrCreate($client, $settingsService, $settingsServiceOption, $value);
        }
    }
}
