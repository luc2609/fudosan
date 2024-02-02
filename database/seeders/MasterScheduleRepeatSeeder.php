<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MasterScheduleRepeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_schedule_repeats')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_schedule_repeats')->insert([
            [
                'repeat' => 'なし',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'repeat' => '毎日',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'repeat' => '毎週',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'repeat' => '毎月',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],

        ]);
    }
}
