<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterColumnInPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->text('avatar')->nullable()->change();
            $table->date('construction_date')->nullable()->change();
        });

        DB::statement("ALTER TABLE properties CHANGE COLUMN contract_type_id contract_type_id TINYINT NULL");
        DB::statement("ALTER TABLE properties CHANGE COLUMN current_situation_id current_situation_id TINYINT NULL");
        DB::statement("ALTER TABLE properties CHANGE COLUMN properties_type_id properties_type_id TINYINT NULL");
        DB::statement("ALTER TABLE properties CHANGE COLUMN building_structure_id building_structure_id TINYINT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
