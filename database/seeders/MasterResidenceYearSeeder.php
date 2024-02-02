<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MasterResidenceYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_residence_years')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_residence_years')->insert([
            [
                'min' => null, 'max' => 1,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'min' => 2, 'max' => 5,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'min' => 6, 'max' => 10,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'min' => 11, 'max' => 15,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'min' => 16, 'max' => 20,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'min' => 21, 'max' => null,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]
        ]);
    }
}
