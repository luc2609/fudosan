<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterNotifyCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_notify_calendars')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_notify_calendars')->insert([
            [
                'notify' => ' なし',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'notify' => 'イベント発生時',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'notify' => '5分前',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'notify' => '15分前',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'notify' => '30分前',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'notify' => '1時間前',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'notify' => '1日前',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
        ]);
    }
}
