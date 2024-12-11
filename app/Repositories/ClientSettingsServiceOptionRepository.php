<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Helpers\StringHelper;
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

    public function getUpsellHeader(Client $client): ?string
    {
        $result = ClientSettingsServiceOption::where('client_id', $client->getId())
            ->where('settings_service_id', SettingsService::UPSELL_HEADER)
            ->first();
        if ($result !== null) {
            return $result->getAttribute('value');
        }
        return null;
    }

    public function getUpsellCompanyName(Client $client): ?string
    {
        $result = ClientSettingsServiceOption::where('client_id', $client->getId())
            ->where('settings_service_id', SettingsService::UPSELL_COMPANY_NAME)
            ->first();
        if ($result !== null) {
            return $result->getAttribute('value');
        }
        return null;
    }

    public function getUpsellCin(Client $client): ?string
    {
        $result = ClientSettingsServiceOption::where('client_id', $client->getId())
            ->where('settings_service_id', SettingsService::UPSELL_CIN)
            ->first();
        if ($result !== null) {
            return $result->getAttribute('value');
        }
        return null;
    }

    public function getUpsellTin(Client $client): ?string
    {
        $result = ClientSettingsServiceOption::where('client_id', $client->getId())
            ->where('settings_service_id', SettingsService::UPSELL_TIN)
            ->first();
        if ($result !== null) {
            return $result->getAttribute('value');
        }
        return null;
    }

    public function getUpsellCompanyAddress(Client $client): ?string
    {
        $result = ClientSettingsServiceOption::where('client_id', $client->getId())
            ->where('settings_service_id', SettingsService::UPSELL_COMPANY_ADDRESS)
            ->first();
        if ($result !== null) {
            return $result->getAttribute('value');
        }
        return null;
    }

    public function getUpsellOrders(Client $client): ?string
    {
        $result = ClientSettingsServiceOption::where('client_id', $client->getId())
            ->where('settings_service_id', SettingsService::UPSELL_ORDERS)
            ->first();
        if ($result !== null) {
            return $result->getAttribute('value');
        }
        return null;
    }

    public function getDynamicPreviewImagesInfiniteRepeat(Client $client): ?string
    {
        $result = ClientSettingsServiceOption::where('client_id', $client->getId())
            ->where('settings_service_id', SettingsService::DYNAMIC_PREVIEW_IMAGES_INFINITE_REPEAT)
            ->first();
        if ($result !== null) {
            return $result->settingsServiceOption()->first()?->getValue();
        }
        return null;
    }

    public function getDynamicPreviewImagesReturnToDefault(Client $client): ?string
    {
        $result = ClientSettingsServiceOption::where('client_id', $client->getId())
            ->where('settings_service_id', SettingsService::DYNAMIC_PREVIEW_IMAGES_RETURN_TO_DEFAULT)
            ->first();
        if ($result !== null) {
            return $result->settingsServiceOption()->first()?->getValue();
        }
        return null;
    }

    public function getDynamicPreviewImagesShowTime(Client $client): ?string
    {
        $result = ClientSettingsServiceOption::where('client_id', $client->getId())
            ->where('settings_service_id', SettingsService::DYNAMIC_PREVIEW_IMAGES_SHOW_TIME)
            ->first();
        if ($result !== null) {
            return $result->settingsServiceOption()->first()?->getValue();
        }
        return null;
    }

    public function getDynamicPreviewImagesInitialLoop(Client $client): ?string
    {
        $result = ClientSettingsServiceOption::where('client_id', $client->getId())
            ->where('settings_service_id', SettingsService::DYNAMIC_PREVIEW_IMAGES_INITIAL_LOOP)
            ->first();
        if ($result !== null) {
            return $result->settingsServiceOption()->first()?->getValue();
        }
        return null;
    }

    public function getDynamicPreviewImagesApplyTo(Client $client): ?string
    {
        $result = ClientSettingsServiceOption::where('client_id', $client->getId())
            ->where('settings_service_id', SettingsService::DYNAMIC_PREVIEW_IMAGES_APPLY_TO)
            ->first();
        if ($result !== null) {
            return $result->settingsServiceOption()->first()?->getValue();
        }
        return null;
    }

    public function getDynamicPreviewImagesMobileIcons(Client $client): ?string
    {
        $result = ClientSettingsServiceOption::where('client_id', $client->getId())
            ->where('settings_service_id', SettingsService::DYNAMIC_PREVIEW_IMAGES_MOBILE_ICONS)
            ->first();
        if ($result !== null) {
            return $result->settingsServiceOption()->first()?->getValue();
        }
        return null;
    }

    public function getEasyUpsellSubscribed(Client $client): bool
    {
        $result = ClientSettingsServiceOption::where('client_id', $client->getId())
            ->where('settings_service_id', SettingsService::UPSELL_SUBSCRIBE)
            ->first();
        if ($result !== null) {
            return StringHelper::bool($result->settingsServiceOption()->first()?->getValue());
        }
        return true;
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
        if ($settingsServiceOption === null) {
            $settingsServiceOption = null;
        } else {
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
