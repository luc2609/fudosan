<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->where('id', '1')->update(['name' => 'スーパーアドミン']);
        DB::table('roles')->where('id', '2')->update(['name' => ' アドミン']);
        DB::table('roles')->where('id', '3')->update(['name' => 'マネージャー']);
        DB::table('roles')->where('id', '4')->update(['name' => '営業担当者']);
    }
}
