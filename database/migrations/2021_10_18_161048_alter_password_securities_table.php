<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPasswordSecuritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('password_securities', function (Blueprint $table) {
            $table->boolean('cms_security_enable')->default(true)->after('security_enable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('password_securities', function (Blueprint $table) {
            $table->dropColumn('cms_security_enable');
        });
    }
}
