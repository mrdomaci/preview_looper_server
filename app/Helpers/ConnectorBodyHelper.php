<?php
declare(strict_types=1);

namespace App\Helpers;

class ConnectorBodyHelper
{
    private const TEMPLATE_INCLUDES = '{
        "data": {
          "snippets": [
            {
              "location": "common-header",
              "html": "<div id=\'preview-looper-settings\' data-infinite-repeat=\'%s\' data-return-to-default=\'%s\' data-show-time=\'%s\'></div>"
            }
          ]
        }
      }';
    
    public static function getStringBodyForTemplateInclude(bool $infiniteRepeat, bool $returnToDefault, int $showTime): string
    {
        $infiniteRepeat = $infiniteRepeat ? '1' : '0';
        $returnToDefault = $returnToDefault ? '1' : '0';
        $showTime = (string) $showTime;
        return sprintf(self::TEMPLATE_INCLUDES, $infiniteRepeat, $returnToDefault, $showTime);
    }
}