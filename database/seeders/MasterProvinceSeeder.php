<?php

namespace Database\Seeders;

use App\Imports\ImportMasterProvince;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MasterProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_provinces')->truncate();
        $file = storage_path('data-csv/master_province.csv');
        Excel::import(new ImportMasterProvince, $file);
    }
}
