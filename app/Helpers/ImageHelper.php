<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Enums\OrderSatusEnum;
use App\Models\Client;
use App\Models\ClientSettingsServiceOption;
use App\Models\SettingsService;
use Gregwar\Image\Image;

class ImageHelper
{
    /**
     * @param Client $client
     * @return bool
    */
    public static function orderStatus(Client $client, ClientSettingsServiceOption $clientSettingsServiceOption): bool
    {
        $header = $clientSettingsServiceOption->getAttribute('value');
        $settingsService = SettingsService::where('id', $clientSettingsServiceOption->getAttribute('settings_service_id'))->first();
        if ($settingsService === null) {
            return false;
        }
        $icon = OrderSatusEnum::getIcon($settingsService);
        $background = '0xFFFFFF';
        $textColor = '0x000000';
        $image = Image::create(300, 300)
                    ->fill($background)
                    ->merge(Image::open('storage/app/images/icons/' . $icon), 22, 47, 256, 256)
                    ->write('public/fonts/Ubuntu/Ubuntu-Regular.ttf', $header, 150, 46, 26, 0, $textColor, 'center')
                    ->setFallback('storage/app/images/icons/plus.png')
                    ->save('storage/app/images/order-status/' . $client->getAttribute('id') . '_' . $icon, 'png', 100);
        if ($image === false) {
            return false;
        }
        return true;
    }
}