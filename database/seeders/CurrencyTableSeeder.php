<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currencies')->insertOrIgnore([
            ['code' => 'EUR', 'format' => '{price} €'],
            ['code' => 'GBP', 'format' => '{price} £'],
            ['code' => 'CHF', 'format' => 'CHF {price}'],
            ['code' => 'SEK', 'format' => '{price} SEK'],
            ['code' => 'NOK', 'format' => 'NOK {price}'],
            ['code' => 'DKK', 'format' => 'DKK {price}'],
            ['code' => 'PLN', 'format' => '{price} PLN'],
            ['code' => 'CZK', 'format' => '{price} Kč'],
            ['code' => 'HUF', 'format' => '{price} HUF'],
            ['code' => 'RON', 'format' => '{price} RON'],
            ['code' => 'HRK', 'format' => '{price} HRK'],
            ['code' => 'BGN', 'format' => '{price} BGN'],
            ['code' => 'ISK', 'format' => 'ISK {price}'],
            ['code' => 'RSD', 'format' => '{price} RSD'],
            ['code' => 'TRY', 'format' => 'TRY {price}'],
            ['code' => 'UAH', 'format' => 'UAH {price}'],
            ['code' => 'BYN', 'format' => 'BYN {price}'],
            ['code' => 'MDL', 'format' => 'MDL {price}'],
            ['code' => 'MKD', 'format' => 'MKD {price}'],
            ['code' => 'ALL', 'format' => 'ALL {price}'],
            ['code' => 'BAM', 'format' => 'BAM {price}'],
            ['code' => 'XCD', 'format' => 'XCD {price}'],
            ['code' => 'GEL', 'format' => 'GEL {price}'],
            ['code' => 'AMD', 'format' => 'AMD {price}'],
            ['code' => 'AZN', 'format' => 'AZN {price}'],
            ['code' => 'KZT', 'format' => 'KZT {price}'],
            ['code' => 'TMT', 'format' => 'TMT {price}'],
            ['code' => 'UZS', 'format' => 'UZS {price}'],
            ['code' => 'KGS', 'format' => 'KGS {price}'],
            ['code' => 'TJS', 'format' => 'TJS {price}'],
            ['code' => 'GIP', 'format' => 'GIP {price}'],
            ['code' => 'GGP', 'format' => 'GGP {price}'],
            ['code' => 'JEP', 'format' => 'JEP {price}'],
            ['code' => 'IMP', 'format' => 'IMP {price}'],
            ['code' => 'FKP', 'format' => 'FKP {price}'],
            ['code' => 'SHG', 'format' => 'SHG {price}'],
            ['code' => 'SHP', 'format' => 'SHP {price}'],
            ['code' => 'AED', 'format' => 'AED {price}'],
            ['code' => 'BHD', 'format' => 'BHD {price}'],
            ['code' => 'ILS', 'format' => '{price} ₪'],
            ['code' => 'JOD', 'format' => 'JOD {price}'],
            ['code' => 'KWD', 'format' => 'KWD {price}'],
            ['code' => 'LBP', 'format' => 'LBP {price}'],
            ['code' => 'OMR', 'format' => 'OMR {price}'],
            ['code' => 'QAR', 'format' => 'QAR {price}'],
            ['code' => 'SAR', 'format' => 'SAR {price}'],
        ]);
    }
}
