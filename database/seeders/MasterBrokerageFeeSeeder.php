<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterBrokerageFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_brokerage_fees')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_brokerage_fees')->insert([
            [
                'min' => null,
                'max' => 500000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'min' => 510000,
                'max' => 1000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'min' => 1010000,
                'max' => 1500000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'min' => 1510000,
                'max' => 2000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'min' => 2010000,
                'max' => null,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ]
        ]);
    }
}
