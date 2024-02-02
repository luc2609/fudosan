<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMasterResidenceYearsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_residence_years', function (Blueprint $table) {
            $table->dropColumn('residence_year');
            $table->double('min')->after('id')->nullable();
            $table->double('max')->after('min')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_residence_years', function (Blueprint $table) {
            $table->string('residence_year');
            $table->dropColumn('min');
            $table->dropColumn('max');
        });
    }
}
