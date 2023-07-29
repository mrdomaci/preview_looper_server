<?php

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
        DB::table('settings_service_options')->insert([
            [
                'settings_service_id' => 1,
                'name' => 'dynamic-preview-images.infinite_repeat_enabled',
                'value' => '1',
                'is_default' => 1,
            ],
            [
                'settings_service_id' => 1,
                'name' => 'dynamic-preview-images.infinite_repeat_disabled',
                'value' => '0',
                'is_default' => 0,
            ],
            [
                'settings_service_id' => 2,
                'name' => 'dynamic-preview-images.infinite_repeat_enabled',
                'value' => '1',
                'is_default' => 1,
            ],
            [
                'settings_service_id' => 2,
                'name' => 'dynamic-preview-images.infinite_repeat_disabled',
                'value' => '0',
                'is_default' => 0,
            ],
            [
                'settings_service_id' => 3,
                'name' => 'dynamic-preview-images.show_time_short',
                'value' => '1000',
                'is_default' => 0,
            ],
            [
                'settings_service_id' => 3,
                'name' => 'dynamic-preview-images.show_time_medium',
                'value' => '1500',
                'is_default' => 1,
            ],
            [
                'settings_service_id' => 3,
                'name' => 'dynamic-preview-images.show_time_long',
                'value' => '2000',
                'is_default' => 0,
            ],
        ]);
    }
}



