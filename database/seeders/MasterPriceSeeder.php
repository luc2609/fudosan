<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_prices')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_prices')->insert([
            [
                'price' => 5000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 10000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 15000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 20000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 25000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 30000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 35000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 40000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 45000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 50000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 55000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 60000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 65000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 70000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 75000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 80000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 85000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 90000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 95000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'price' => 100000000,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
        ]);
    }
}
