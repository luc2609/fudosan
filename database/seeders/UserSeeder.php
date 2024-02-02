<?php

namespace Database\Seeders;

use App\Models\Company;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('ja_JP');
        $currentTime = date('Y-m-d H:i:s');
        $companies = Company::all();
        $users = [];
        foreach ($companies as $company) {
            $companyId = $company->id;
            $limit = random_int(20, 50);
            for ($i = 0; $i < $limit; $i++) {
                $attribute = [
                    'email' => $faker->email,
                    'password' => Hash::make('Amela123'),
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                    'kana_first_name' => $faker->firstName(),
                    'kana_last_name' => $faker->lastName(),
                    'email_verified_at' => $currentTime,
                    'remember_token' => Str::random(10),
                    'dob' => $faker->date($format = 'Y-m-d', $max = 'now'),
                    'position' => random_int(1, 5),
                    'gender' => random_int(1, 2),
                    'phone' => $faker->phoneNumber(),
                    'postcode' => '0600042',
                    'address' => $faker->address(),
                    'avatar' => 'https://ict-imgs.vgcloud.vn/2020/09/01/19/huong-dan-tao-facebook-avatar.jpg',
                    'status' => $faker->randomElement([1, 2]),
                    'flag' => $faker->randomElement([1, 2]),
                    'company' => $companyId,
                    'commission_rate' => $faker->numberBetween(0, 100),
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                ];
                array_push($users, $attribute);
            }
        }
        DB::table('users')->insert($users);
    }
}
