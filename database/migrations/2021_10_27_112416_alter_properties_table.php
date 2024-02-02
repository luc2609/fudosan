<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->renameColumn('prefectures', 'province');
            $table->renameColumn('municipalities', 'district');
        });

        Schema::rename('master_postal_code', 'master_postal_codes');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->renameColumn('province', 'prefectures');
            $table->renameColumn('district', 'municipalities');
        });

        Schema::rename('master_postal_codes', 'master_postal_code');
    }
}
