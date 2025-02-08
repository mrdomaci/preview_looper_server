<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsServiceOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings_service_options')->insertOrIgnore([
            [
                'id' => 33,
                'settings_service_id' => 1,
                'name' => 'dynamic-preview-images.infinite_repeat_enabled',
                'value' => '1',
                'is_default' => 1,
            ],
            [
                'id' => 34,
                'settings_service_id' => 1,
                'name' => 'dynamic-preview-images.infinite_repeat_disabled',
                'value' => '0',
                'is_default' => 0,
            ],
            [
                'id' => 35,
                'settings_service_id' => 2,
                'name' => 'dynamic-preview-images.return_to_default_enabled',
                'value' => '1',
                'is_default' => 1,
            ],
            [
                'id' => 36,
                'settings_service_id' => 2,
                'name' => 'dynamic-preview-images.return_to_default_disabled',
                'value' => '0',
                'is_default' => 0,
            ],
            [
                'id' => 37,
                'settings_service_id' => 3,
                'name' => 'dynamic-preview-images.show_time_short',
                'value' => '1000',
                'is_default' => 0,
            ],
            [
                'id' => 38,
                'settings_service_id' => 3,
                'name' => 'dynamic-preview-images.show_time_medium',
                'value' => '1500',
                'is_default' => 1,
            ],
            [
                'id' => 39,
                'settings_service_id' => 3,
                'name' => 'dynamic-preview-images.show_time_long',
                'value' => '2000',
                'is_default' => 0,
            ],
            [
                'id' => 40,
                'settings_service_id' => 4,
                'name' => 'dynamic-preview-images.show_time_none',
                'value' => '0',
                'is_default' => 0,
            ],
            [
                'id' => 41,
                'settings_service_id' => 4,
                'name' => 'dynamic-preview-images.show_time_very_short',
                'value' => '500',
                'is_default' => 1,
            ],
            [
                'id' => 42,
                'settings_service_id' => 4,
                'name' => 'dynamic-preview-images.show_time_short',
                'value' => '1000',
                'is_default' => 0,
            ],
            [
                'id' => 43,
                'settings_service_id' => 4,
                'name' => 'dynamic-preview-images.show_time_medium',
                'value' => '1500',
                'is_default' => 0,
            ],
            [
                'id' => 44,
                'settings_service_id' => 4,
                'name' => 'dynamic-preview-images.show_time_long',
                'value' => '2000',
                'is_default' => 0,
            ],
            [
                'id' => 45,
                'settings_service_id' => 5,
                'name' => 'dynamic-preview-images.apply_to_all',
                'value' => 'all',
                'is_default' => 1,
            ],
            [
                'id' => 46,
                'settings_service_id' => 5,
                'name' => 'dynamic-preview-images.apply_to_desktop',
                'value' => 'pc',
                'is_default' => 0,
            ],
            [
                'id' => 47,
                'settings_service_id' => 5,
                'name' => 'dynamic-preview-images.apply_to_mobile',
                'value' => 'mobile',
                'is_default' => 0,
            ],
            [
                'id' => 48,
                'settings_service_id' => 6,
                'name' => 'dynamic-preview-images.circle_icon',
                'value' => 'circles',
                'is_default' => 1,
            ],
            [
                'id' => 49,
                'settings_service_id' => 6,
                'name' => 'dynamic-preview-images.no_icon',
                'value' => 'none',
                'is_default' => 0,
            ],
            [
                'id' => 50,
                'settings_service_id' => 6,
                'name' => 'dynamic-preview-images.number_icon',
                'value' => 'numbers',
                'is_default' => 0,
            ],
            [
                'id' => 51,
                'settings_service_id' => 7,
                'name' => 'easy-upsell.one',
                'value' => '1',
                'is_default' => 0,
            ],
            [
                'id' => 52,
                'settings_service_id' => 7,
                'name' => 'easy-upsell.two',
                'value' => '2',
                'is_default' => 0,
            ],
            [
                'id' => 53,
                'settings_service_id' => 7,
                'name' => 'easy-upsell.three',
                'value' => '3',
                'is_default' => 0,
            ],
            [
                'id' => 54,
                'settings_service_id' => 7,
                'name' => 'easy-upsell.four',
                'value' => '4',
                'is_default' => 1,
            ],
            [
                'id' => 55,
                'settings_service_id' => 7,
                'name' => 'easy-upsell.five',
                'value' => '5',
                'is_default' => 0,
            ],
            [
                'id' => 56,
                'settings_service_id' => 7,
                'name' => 'easy-upsell.six',
                'value' => '6',
                'is_default' => 0,
            ],
            [
                'id' => 57,
                'settings_service_id' => 7,
                'name' => 'easy-upsell.seven',
                'value' => '7',
                'is_default' => 0,
            ],
            [
                'id' => 58,
                'settings_service_id' => 7,
                'name' => 'easy-upsell.eight',
                'value' => '8',
                'is_default' => 0,
            ],
            [
                'id' => 59,
                'settings_service_id' => 14,
                'name' => 'general.subscribe',
                'value' => 1,
                'is_default' => 1,
            ],
            [
                'id' => 60,
                'settings_service_id' => 14,
                'name' => 'general.unsubscribe',
                'value' => 0,
                'is_default' => 0,
            ],
            [
                'id' => 61,
                'settings_service_id' => 15,
                'name' => 'easy-upsell.mixed_recommendations',
                'value' => 'mixed',
                'is_default' => 1,
            ],
            [
                'id' => 62,
                'settings_service_id' => 15,
                'name' => 'easy-upsell.orders_only_recommendations',
                'value' => 'orders_only',
                'is_default' => 0,
            ],
            [
                'id' => 63,
                'settings_service_id' => 15,
                'name' => 'easy-upsell.categories_only_recommendations',
                'value' => 'categories_only',
                'is_default' => 0,
            ],
        ]);
    }
}
