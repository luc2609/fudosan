<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterContactReasonSeeder extends Seeder
{
    public function run()
    {
        DB::table('master_contact_reasons')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_contact_reasons')->insert([
            [
                'name' => 'スケジュール管理',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '案件管理',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '顧客管理',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '物件管理',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => 'ランキング機能',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '不具合',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => 'その他',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
        ]);
    }
}
