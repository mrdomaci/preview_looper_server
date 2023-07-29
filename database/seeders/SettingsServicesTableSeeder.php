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
        DB::table('settings_services')->insert([
            [
                'name' => 'dynamic-preview-images.infinite_repeat',
                'sort' => 1,
                'service_id' => 1,
                'type' => 'select',
            ],
            [
                'name' => 'dynamic-preview-images.return_to_default',
                'sort' => 2,
                'service_id' => 1,
                'type' => 'select',
            ],
            [
                'name' => 'dynamic-preview-images.show_time',
                'sort' => 3,
                'service_id' => 1,
                'type' => 'select',
            ],
        ]);
    }
}
