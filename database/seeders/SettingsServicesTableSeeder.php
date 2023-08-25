<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings_services')->insertOrIgnore([
            [
                'id' => 1,
                'name' => 'dynamic-preview-images.infinite_repeat',
                'sort' => 1,
                'service_id' => 1,
                'type' => 'select',
            ],
            [
                'id' => 2,
                'name' => 'dynamic-preview-images.return_to_default',
                'sort' => 2,
                'service_id' => 1,
                'type' => 'select',
            ],
            [
                'id' => 3,
                'name' => 'dynamic-preview-images.show_time',
                'sort' => 3,
                'service_id' => 1,
                'type' => 'select',
            ],
            [
                'id' => 4,
                'name' => 'dynamic-preview-images.initial_loop',
                'sort' => 4,
                'service_id' => 1,
                'type' => 'select',
            ],
            [
                'id' => 5,
                'name' => 'dynamic-preview-images.apply_to',
                'sort' => 5,
                'service_id' => 1,
                'type' => 'select',
            ],
            [
                'id' => 6,
                'name' => 'dynamic-preview-images.mobile_icons',
                'sort' => 6,
                'service_id' => 1,
                'type' => 'select',
            ]
        ]);
    }
}
