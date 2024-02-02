<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('ja_JP');
        $limit = 50;
        $companies = [];
        $currentTime = date('Y-m-d H:i:s');
        for ($i = 0; $i < $limit; $i++) {
            $company = [
                'name' => $faker->company,
                'province' => $faker->prefecture,
                'district' => $faker->city,
                'street' => $faker->streetName,
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
                'website' => $faker->url,
                'status' => 1,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
                'commission_rate' => $faker->randomNumber(2, true),
            ];

            array_push($companies, $company);
        }
        DB::table('companies')->insert($companies);
    }
}
