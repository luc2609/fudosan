<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSalePurposeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_sale_purposes')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_sale_purposes')->insert([
            [
                'sale_purpose' => '住み替え',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'sale_purpose' => '相続',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'sale_purpose' => '生産整理',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'sale_purpose' => '転職',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'sale_purpose' => '金銭的',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'sale_purpose' => '離婚 ',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'sale_purpose' => 'その他 ',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ]
        ]);
    }
}
