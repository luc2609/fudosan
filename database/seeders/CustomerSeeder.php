<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MasterPostalCode;
use App\Models\User;
use Faker\Factory;

class CustomerSeeder extends Seeder
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $customers = [];
        $customerAdvertisingForms = [];
        $customerPurchasePurposes = [];

        foreach ($companies as $company) {
            $companyId = $company->id;
            $userIds = User::where('users.company', $companyId)->pluck('id');
            $limit = random_int(20, 50);
            $masterPostcodes = MasterPostalCode::inRandomOrder()->limit($limit)->get();
            for ($i = 0; $i < $limit; $i++) {
                $customer = [
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                    'kana_first_name' => $faker->firstName(),
                    'kana_last_name' => $faker->lastName(),
                    'birthday' => $faker->date($format = 'Y-m-d', $max = 'now'),
                    'phone' =>  0 . random_int(100000000, 9999999999),
                    'email' => $faker->unique()->safeEmail(),
                    'postal_code' => $masterPostcodes[$i]->postal_code,
                    'province' => $masterPostcodes[$i]->province,
                    'district' => $masterPostcodes[$i]->district,
                    'address' => $masterPostcodes[$i]->street ?? ' ',
                    'residence_year_id' => random_int(1, 6),
                    'budget' => rand(100000000, 5000000000),
                    'deposit' => rand(10000000, 50000000),
                    'purchase_time' => $currentTime,
                    'memo' => $faker->sentence(15),
                    'contact_method_id' => random_int(1, 4),
                    'status' => APPROVED_CUSTOMER,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime,
                    'company_id' => $companyId,
                    'create_by_id' => $userIds->random(),
                ];
                array_push($customers, $customer);
            }
        }
        DB::table('customers')->insert($customers);

        $customerIds = Customer::all()->pluck('id');
        foreach ($customerIds as $customerId) {
            $customerAdvertisingForm = [
                'customer_id' => $customerId,
                'advertising_form_id' => random_int(1, 12),
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];

            $customerPurchasePurpose = [
                'customer_id' => $customerId,
                'purchase_purpose_id' => random_int(1, 9),
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];
            array_push($customerAdvertisingForms, $customerAdvertisingForm);
            array_push($customerPurchasePurposes, $customerPurchasePurpose);
        }

        DB::table('customer_advertising_forms')->insert($customerAdvertisingForms);
        DB::table('customer_purchase_purposes')->insert($customerPurchasePurposes);
    }
}
