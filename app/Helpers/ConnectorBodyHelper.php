<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Models\Client;
use App\Models\ClientSettingsServiceOption;
use App\Models\Service;
use App\Models\SettingsService;
use App\Models\SettingsServiceOption;

class ConnectorBodyHelper
{
    private const TEMPLATE_INCLUDES = '{
        "data": {
          "snippets": [
            {
              "location": "common-header",
              "html": "%s"
            }
          ]
        }
      }';
    
    public static function getStringBodyForTemplateInclude(Service $service, Client $client): string
    {
        $htmlString = "<div id='%s'";
        $htmlString = sprintf($htmlString, $service->getAttribute('name'));
        $clientSettingsService = SettingsService::where('service_id', $service->getAttribute('id'))->get();
        foreach ($clientSettingsService as $settingsService) {
            $clientSettingsServiceOption = ClientSettingsServiceOption::where('client_id', $client->getAttribute('id'))->where('settings_service_id', $settingsService->getAttribute('id'))->first();
            if ($clientSettingsServiceOption === null) {
                continue;
            }
            $settingsServiceOption = SettingsServiceOption::where('id', $clientSettingsServiceOption->getAttribute('settings_service_option_id'))->first();
            if ($settingsServiceOption === null) {
                continue;
            }
            $htmlString .= sprintf(" data-%s='%s'", $settingsService->getAttribute('name'), $settingsServiceOption->getAttribute('value'));
        }
        $htmlString .= "></div>";
        return sprintf(self::TEMPLATE_INCLUDES, $htmlString);
    }
}