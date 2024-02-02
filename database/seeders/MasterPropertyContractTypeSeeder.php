<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterPropertyContractTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_property_contract_types')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_property_contract_types')->insert([
            [
                'name' => '仲介（一般）',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '仲介（専属）',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '仲介（専任）',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '専任',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'name' => '代理',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]
        ]);
    }
}
