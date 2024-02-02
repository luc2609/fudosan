<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterPropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_property_types')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_property_types')->insert([
            [
                'name' => '土地',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '戸建',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => 'マンション',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]
        ]);
    }
}
