<?php

namespace Database\Seeders;

use App\Imports\ImportMasterPostalCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MasterPostalCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_postal_codes')->truncate();
        $file = storage_path('data-csv/kenall.csv');
        Excel::import(new ImportMasterPostalCode, $file);
    }
}
