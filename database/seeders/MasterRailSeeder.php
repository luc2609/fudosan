<?php

namespace Database\Seeders;

use App\Imports\ImportMasterRail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MasterRailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('master_rails')->truncate();
        $file = storage_path('data-csv/master_rail.csv');
        Excel::import(new ImportMasterRail, $file);
    }
}
