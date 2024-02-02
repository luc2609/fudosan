<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\MasterPostalCode;
use App\Models\MasterProvince;
use App\Models\MasterStation;
use App\Models\Property;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create('ja_JP');
        $properties = [];
        $propertyStations = [];
        $companies = Company::all();
        $lastProperty = Property::orderBy('id', 'desc')->first();
        $lastPropertyId = $lastProperty ?  $lastProperty->id : 0;

        $currentTime = date('Y-m-d H:i:s');
        foreach ($companies as $company) {
            $companyId = $company->id;
            $limit = random_int(20, 100);
            $userIds = User::where('users.company', $companyId)->pluck('users.id');
            $masterPostcodes = MasterPostalCode::inRandomOrder()->limit($limit)->get();
            for ($i = 0; $i < $limit; $i++) {
                $price = random_int(100000, 10000000);
                $brokerageFee = 0;

                if ($price < 2000000) {
                    $brokerageFee = $price * 0.05;
                } else if ($price <= 4000000) {
                    $brokerageFee = $price * 0.04 + 20000;
                } else if ($price > 4000000) {
                    $brokerageFee = $price * 0.03 + 60000;
                }

                $tax = $brokerageFee * 0.1;
                $brokerageFee = $brokerageFee + $tax;

                $property = [
                    'avatar' => 'https://images.unsplash.com/photo-1582407947304-fd86f028f716?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MXx8cmVhbCUyMGVzdGF0ZXxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&w=1000&q=80',
                    'name' => $faker->name,
                    'construction_date' => $faker->date,
                    'postal_code' => $masterPostcodes[$i]->postal_code,
                    'province' => $masterPostcodes[$i]->province,
                    'district' => $masterPostcodes[$i]->district,
                    'address' => $masterPostcodes[$i]->street ?? ' ',
                    'price' => $price,
                    'contract_type_id' => random_int(1, 5),
                    'properties_type_id' => random_int(1, 3),
                    'building_structure_id' => random_int(1, 5),
                    'status' => random_int(1, 3),
                    'created_id' => $userIds->random(),
                    'company_id' => $companyId,
                    'created_at' => $currentTime,
                    'updated_at' => $currentTime
                ];

                $provinceCd = MasterProvince::where('name', $masterPostcodes[$i]->province)->first()->cd;
                $masterStations = MasterStation::where('province_cd', $provinceCd)->inRandomOrder()->limit(3)->get();

                foreach ($masterStations as $masterStation) {
                    $propertyStation = [
                        'property_id' =>  $lastPropertyId + 1 + $i,
                        'rail_cd' => $masterStation->rail_cd,
                        'station_cd' => $masterStation->cd,
                        'on_foot' => random_int(1, 999)
                    ];

                    array_push($propertyStations, $propertyStation);
                }

                array_push($properties, $property);
            }
        }

        DB::table('properties')->insert($properties);
        DB::table('property_stations')->insert($propertyStations);
    }
}
