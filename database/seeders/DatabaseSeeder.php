<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call([
            MasterPositionSeeder::class,
            MasterAdvertisingFormSeeder::class,
            MasterAdvertisingWebSeeder::class,
            MasterPostalCodeSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
            MasterPropertyContractTypeSeeder::class,
            MasterPropertyCurrentSituationSeeder::class,
            MasterPropertyTypeSeeder::class,
            MasterBuildingStructureSeeder::class,
            MasterProvinceSeeder::class,
            MasterRailSeeder::class,
            MasterStationSeeder::class,
            MasterPriceSeeder::class,
            MasterBrokerageFeeSeeder::class,
            MasterResidenceYearSeeder::class,
            MasterContactMethodSeeder::class,
            MasterPurchasePuposesSeeder::class,
            MasterSalePurposeSeeder::class,
            MasterPhaseProjectSeeder::class,
            MasterScheduleRepeatSeeder::class,
            MasterNotifyCalendarSeeder::class,
            UpdateRoleSeeder::class,
            MasterContactReasonSeeder::class,
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
