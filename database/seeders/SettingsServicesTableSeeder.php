<?php

declare(strict_types=1);

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
            ],
            [
                'id' => 7,
                'name' => 'easy-upsell.amount',
                'sort' => 1,
                'service_id' => 2,
                'type' => 'select',
            ],
            [
                'id' => 8,
                'name' => 'easy-upsell.title',
                'sort' => 2,
                'service_id' => 2,
                'type' => 'value',
            ],
            [
                'id' => 9,
                'name' => 'easy-upsell.company_name',
                'sort' => 3,
                'service_id' => 2,
                'type' => 'value',
            ],
            [
                'id' => 10,
                'name' => 'easy-upsell.cin',
                'sort' => 4,
                'service_id' => 2,
                'type' => 'value',
            ],
            [
                'id' => 11,
                'name' => 'easy-upsell.tin',
                'sort' => 5,
                'service_id' => 2,
                'type' => 'value',
            ],
            [
                'id' => 12,
                'name' => 'easy-upsell.company_address',
                'sort' => 6,
                'service_id' => 2,
                'type' => 'value',
            ],
            [
                'id' => 13,
                'name' => 'easy-upsell.monthly_orders',
                'sort' => 7,
                'service_id' => 2,
                'type' => 'hidden',
            ],
        ]);
    }
}
