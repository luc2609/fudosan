<?php

namespace Database\Seeders;

use App\Models\Company;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = Company::all();
        $limit = 10;
        $faker = Factory::create('ja_JP');
        $currentTime = date('Y-m-d H:i:s');
        $divisions = [];
        foreach ($companies as $company) {
            $companyId = $company->id;
            for ($i = 0; $i < $limit; $i++) {
                $params = [
                    'name' => $faker->name,
                    'company_id' => $companyId,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                ];
                array_push($divisions, $params);
            }
        }
        DB::table('divisions')->insert($divisions);
    }
}
