<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAdvertisingWebSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_advertising_webs')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_advertising_webs')->insert([
            [
                'name' => ' Yahoo Japan 不動産',
                'URL' => 'https://www.google.com/',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => '自社ホームページ',
                'URL' => 'https://www.google.com/',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => 'スーモ',
                'URL' => 'https://www.google.com/',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => 'LIFULL',
                'URL' => 'https://www.google.com/',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => 'アットホーム',
                'URL' => 'https://www.google.com/',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => 'その他',
                'URL' => ' ',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ]
        ]);
    }
}
