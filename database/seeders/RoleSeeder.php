<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->truncate();
        $roles = [
            [
                'name' => 'Admin CMS System',
                'slug' => 'admin_cms_system',
            ],
            [
                'name' => 'Admin CMS Company',
                'slug' => 'admin_cms_company'
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager'
            ],
            [
                'name' => 'User',
                'slug' => 'user'
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}
