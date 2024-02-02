<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterBuildingStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_property_building_structures')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_property_building_structures')->insert([
            [
                'name' => '木造',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => '軽量鉄骨造',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => '重量鉄骨造',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => '鉄筋コンクリート造',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'name' => '鉄骨鉄筋コンクリート造',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ]
        ]);
    }
}
