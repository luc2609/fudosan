<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\UserRole;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currentTime = date('Y-m-d H:i:s');
        $companies = Company::all();
        $userRoles = [];
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($companies as $company) {
            $companyId = $company->id;
            $userIds = User::where('company', $companyId)->pluck('id')->toArray();
            for ($j = 0; $j < count($userIds); $j++) {
                $userRole = [
                    'user_id' => $userIds[$j],
                    'role_id' => random_int(2, 4),
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ];
                array_push($userRoles, $userRole);
            }
        }
        DB::table('user_roles')->insert($userRoles);
    }
}
