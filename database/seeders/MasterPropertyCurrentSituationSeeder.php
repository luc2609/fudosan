<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterPropertyCurrentSituationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_property_current_situations')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_property_current_situations')->insert([
            [
                'name' => '予約',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '空き',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '済み',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]
        ]);
    }
}
