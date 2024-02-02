<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMasterAdvertisingWebsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_advertising_webs', function (Blueprint $table) {
            $table->renameColumn('URL', 'url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_advertising_webs', function (Blueprint $table) {
            $table->renameColumn('url', 'URL');
        });
    }
}
