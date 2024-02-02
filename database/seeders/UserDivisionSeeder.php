<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserDivisionSeeder extends Seeder
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
        $userDivisions = [];
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($companies as $company) {
            $companyId = $company->id;
            $divisionIds = Division::where('company_id', $companyId)->pluck('id');
            $staffIds = User::where('company', $companyId)
                ->join('user_roles', 'user_roles.user_id', 'users.id')
                ->where('user_roles.role_id', USER)
                ->pluck('users.id')->toArray();
            for ($i = 0; $i < count($staffIds); $i++) {
                $user = User::find($staffIds[$i]);
                $user->division = $divisionIds->random();
                $user->save();
            }

            $managerIds = User::where('company', $companyId)
                ->join('user_roles', 'user_roles.user_id', 'users.id')
                ->where('user_roles.role_id', MANAGER)
                ->pluck('users.id')->toArray();
            for ($i = 0; $i < count($managerIds); $i++) {
                $userDivision = [
                    'user_id' => $managerIds[$i],
                    'division_id' => $divisionIds->random(),
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ];
                array_push($userDivisions, $userDivision);
            }
        }
        DB::table('user_divisions')->insert($userDivisions);
    }
}
