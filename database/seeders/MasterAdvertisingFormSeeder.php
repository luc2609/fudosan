<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterAdvertisingFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_advertising_forms')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_advertising_forms')->insert([
            [
                'name' => '新聞チラシ',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => 'ポストチラシ',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => 'スーモマガジン',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => '看板',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => '弊社HP',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => 'ヤフー不動産',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => 'ホームズ',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => 'スーモ',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => '紹介',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => '現地売出',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => 'ダイレクトメール',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => 'その他',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
        ]);
    }
}
