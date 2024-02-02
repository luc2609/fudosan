<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_fields')->insert([
            [
                'type' => 'integer',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'type' => 'string',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'type' => 'text',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]
        ]);
    }
}
