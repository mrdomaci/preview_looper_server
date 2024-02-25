<?php

declare(strict_types=1);

namespace App\Helpers;

use Coderatio\SimpleBackup\SimpleBackup;

class BackupDBHelper
{
    public static function run(string $path, string $fileName): void
    {
        SimpleBackup::start()
            ->setDbName(env('DB_DATABASE'))
            ->setDbUser(env('DB_USERNAME'))
            ->setDbPassword(env('DB_PASSWORD'))
            ->setDbHost(env('DB_HOST'))
            ->includeOnly(
                [
                    'client_services',
                    'clients',
                    'client_settings_service_options',
                    'product_category_recommendations',
                    'services',
                    'settings_service_options',
                    'settings_services'
                ]
            )->then()->storeAfterExportTo($path, $fileName);
    }
}
