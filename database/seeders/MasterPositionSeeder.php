<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_positions')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_positions')->insert([
            [
                'name' => '主任',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '係長',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '課長',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '次長',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '部長',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]
        ]);
    }
}
