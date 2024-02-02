<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterPhaseProjectSeeder extends Seeder
{
    public function run()
    {
        DB::table('master_phase_projects')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_phase_projects')->insert([
            [
                'name' => '来店',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '見学',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '仮受付申込',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '事前審査',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '契約',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '本審査',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '立会',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '金消契約',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '決済',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '新規',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]
        ]);
    }
}
