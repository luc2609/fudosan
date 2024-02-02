<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterColumnToPropertyAdvertisingWebsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_advertising_webs', function (Blueprint $table) {
            $table->renameColumn('mater_advertising_web_id', 'advertising_web_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_advertising_webs', function (Blueprint $table) {
            $table->renameColumn('advertising_web_id', 'mater_advertising_web_id');
        });
    }
}
