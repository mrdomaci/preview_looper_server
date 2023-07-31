<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Models\Client;
use App\Models\ClientSettingsServiceOption;
use App\Models\Service;

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
        $clientSettingsServiceOptions = ClientSettingsServiceOption::with('settingsService', 'settingsServiceOption')->where('client_id', $client->getAttribute('id'))->get();
        foreach ($clientSettingsServiceOptions as $clientSettingsServiceOption) {
            $htmlString .= sprintf(" data-%s='%s'", $clientSettingsServiceOption->settingsService->getAttribute('name'), $clientSettingsServiceOption->settingsServiceOption->getAttribute('value'));
        }
        $htmlString .= "<\/div>";
        return sprintf(self::TEMPLATE_INCLUDES, $htmlString);
    }
}