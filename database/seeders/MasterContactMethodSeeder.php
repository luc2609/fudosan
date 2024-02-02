<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MasterContactMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_contact_methods')->truncate();
        $currentTime = date('Y-m-d H:i:s');

        DB::table('master_contact_methods')->insert([
            [
                'contact_method' => 'LINE',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'contact_method' => '電話',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'contact_method' => 'メール',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
            [
                'contact_method' => 'その他',
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ],
        ]);
    }
}
