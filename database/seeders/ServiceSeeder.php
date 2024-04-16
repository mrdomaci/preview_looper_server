<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('services')->insertOrIgnore([
            [
                "id" => 1,
                "name" => "dynamic-preview-images",
                "hash" => "e2a74856293c24b3f5ce56f0ea11661af6c480a3",
                "url-path" => "dynamic-preview-images",
                "view-name" => "dynamicPreviewImages",
                "created_at" => "2023-07-22T10:28:10.000Z",
                "updated_at" => null
            ],
            [
                "id" => 2,
                "name" => "easy-upsell",
                "hash" => "1a6dd5047c6967f8d330fcb07aa4b3ad99e14123",
                "url-path" => "easy-upsell",
                "view-name" => "easyUpsell",
                "created_at" => "2023-11-18T16:40:49.000Z",
                "updated_at" => null
            ]
        ]);
    }
}
