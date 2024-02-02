<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Division;
use App\Models\Project;
use App\Models\Property;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('ja_JP');
        $companies = Company::all();
        $projects = [];
        $currentTime = date('Y-m-d H:i:s');

        foreach ($companies as $company) {
            $companyId = $company->id;

            $limit = random_int(20, 50);
            for ($i = 0; $i < $limit; $i++) {
                $divisions = Division::where('company_id', $companyId)->pluck('id');
                $divisionId = $divisions->random();

                $type = random_int(1, 2);
                $project = [
                    'title' => '【新規】' . $faker->name,
                    'division_id' => $divisionId,
                    'company_id' => $companyId,
                    'type' => $type,
                    'price' =>  random_int(100000, 10000000),
                    'deposit_price' => ($type == 1) ? random_int(10000, 1000000) : null,
                    'monthly_price' => ($type == 1) ? random_int(10000, 1000000) : null,
                    'transaction_time' => $faker->date,
                    'description' => 'description' . $faker->text,
                    'revenue' => random_int(10000, 1000000),
                    'history' => '',
                    'current_phase_id' => random_int(1, 9),
                    'close_status' => 0,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                    'purpose' => $faker->text
                ];
                array_push($projects, $project);
            }
        }
        DB::table('projects')->insert($projects);

        $projects = Project::all();
        foreach ($projects as $project) {
            $projectUsers = [];
            $projectCustomers = [];
            $projectPhases = [];
            $projectProperties = [];

            $projectId = $project->id;
            $divisionId = $project->division_id;
            $companyId = $project->company_id;

            $customerIds = Customer::where('company_id', $companyId)->pluck('id');
            $propertyIds = Property::where('company_id', $companyId)->pluck('id');
            $userIds = User::where([
                'company' => $companyId,
                'division' => $divisionId
            ])->orWhereHas('divisions', function ($query) use ($companyId, $divisionId) {
                $query->where([
                    'company' => $companyId,
                    'division_id' => $divisionId
                ]);
            })->pluck('users.id');
            if (count($userIds) > 0) {
                $projectUser1 = [
                    'user_id' => $userIds->random(),
                    'project_id' => $projectId,
                    'is_contract' => 0,
                    'user_type' => USER_IN_CHARGE_TYPE,
                    'brokerage_fee' => random_int(1000, 100000),
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ];
                $projectUser2 = [
                    'user_id' => $userIds->random(),
                    'project_id' => $projectId,
                    'is_contract' => 0,
                    'user_type' => SUB_USER_IN_CHARGE_TYPE,
                    'brokerage_fee' => random_int(1000, 100000),
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ];
                array_push($projectUsers, $projectUser1);
                array_push($projectUsers, $projectUser2);
                DB::table('project_users')->insert($projectUsers);
            }

            $projectCustomer1 = [
                'customer_id' => $customerIds->random(),
                'project_id' => $projectId,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ];
            $projectCustomer2 = [
                'customer_id' => $customerIds->random(),
                'project_id' => $projectId,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ];

            $projectProperty1 = [
                'property_id' => $propertyIds->random(),
                'project_id' => $projectId,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ];
            $projectProperty2 = [
                'property_id' => $propertyIds->random(),
                'project_id' => $projectId,
                'created_at' => $currentTime,
                'updated_at' => $currentTime
            ];

            for ($j = 1; $j < 11; $j++) {
                $projectPhase = [
                    'project_id' => $projectId,
                    'm_phase_project_id' => $j,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ];
                array_push($projectPhases, $projectPhase);
            }

            array_push($projectCustomers, $projectCustomer1);
            array_push($projectCustomers, $projectCustomer2);
            array_push($projectProperties, $projectProperty1);
            array_push($projectProperties, $projectProperty2);

            DB::table('project_customers')->insert($projectCustomers);
            DB::table('project_phases')->insert($projectPhases);
            DB::table('project_properties')->insert($projectProperties);
        }


        $phaseProjects = DB::table('project_phases')->where([
            'm_phase_project_id' => NO_PHASE
        ])->get();

        foreach ($phaseProjects as  $phaseProject) {
            $phaseProjectId = $phaseProject->id;
            $projectId =  $phaseProject->project_id;
            DB::table('projects')->where([
                'id' => $projectId
            ])->update(['current_phase_id' => $phaseProjectId]);
        }
    }
}
