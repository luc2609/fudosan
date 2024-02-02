<?php

namespace Database\Seeders;

use App\Imports\ImportMasterStation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MasterStationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_stations')->truncate();
        $file = storage_path('data-csv/master_station.csv');
        Excel::import(new ImportMasterStation, $file);
    }
}
