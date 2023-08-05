<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsServicesAdditionalTableSeeder extends Seeder
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
                'name' => 'dynamic-preview-images.initial_loop',
                'sort' => 4,
                'service_id' => 1,
                'type' => 'select',
            ],
            [
                'name' => 'dynamic-preview-images.apply_to',
                'sort' => 5,
                'service_id' => 1,
                'type' => 'select',
            ]
        ]);
    }
}
