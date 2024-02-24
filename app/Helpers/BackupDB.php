<?php

declare(strict_types=1);

namespace App\Helpers;

use Coderatio\SimpleBackup\SimpleBackup;
use DateTime;

class BackupDB
{
    public static function run(): void
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
            )->then()->storeAfterExportTo('storage/app/backup', (new DateTime())->format('Y-m-d') . '_backup.sql');
    }
}
