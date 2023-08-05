<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsServiceOptionsAdditionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings_service_options')->insert([
            [
                'settings_service_id' => 4,
                'name' => 'dynamic-preview-images.show_time_none',
                'value' => '0',
                'is_default' => 0,
            ],
            [
                'settings_service_id' => 4,
                'name' => 'dynamic-preview-images.show_time_very_short',
                'value' => '500',
                'is_default' => 1,
            ],
            [
                'settings_service_id' => 4,
                'name' => 'dynamic-preview-images.show_time_short',
                'value' => '1000',
                'is_default' => 0,
            ],
            [
                'settings_service_id' => 4,
                'name' => 'dynamic-preview-images.show_time_medium',
                'value' => '1500',
                'is_default' => 0,
            ],
            [
                'settings_service_id' => 4,
                'name' => 'dynamic-preview-images.show_time_long',
                'value' => '2000',
                'is_default' => 0,
            ],
            [
                'settings_service_id' => 5,
                'name' => 'dynamic-preview-images.apply_to_all',
                'value' => 'all',
                'is_default' => 1,
            ],
            [
                'settings_service_id' => 5,
                'name' => 'dynamic-preview-images.apply_to_desktop',
                'value' => 'pc',
                'is_default' => 0,
            ],
            [
                'settings_service_id' => 5,
                'name' => 'dynamic-preview-images.apply_to_mobile',
                'value' => 'mobile',
                'is_default' => 0,
            ],
        ]);
    }
}



