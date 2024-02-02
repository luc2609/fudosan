<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MasterPurchasePuposesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_purchase_purposes')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_purchase_purposes')->insert([
            [
                'purchase_purpose' => '今の住まいが狭い',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'purchase_purpose' => '今の住まいが古い ',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'purchase_purpose' => '家賃がもったいない',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'purchase_purpose' => '賃貸更新期間',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'purchase_purpose' => '持家希望',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'purchase_purpose' => 'ご結婚',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'purchase_purpose' => 'ご出産 ',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'purchase_purpose' => '転職',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
            [
                'purchase_purpose' => 'その他',
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ],
        ]);
    }
}
