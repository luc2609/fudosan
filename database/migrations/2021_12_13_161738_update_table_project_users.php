<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableProjectUsers extends Migration
{
    public function up()
    {
        Schema::table('project_users', function (Blueprint $table) {
            $table->double('brokeage_fee')->nullable();
        });
    }

    public function down()
    {
        Schema::table('project_users', function (Blueprint $table) {
            $table->dropColumn('brokeage_fee')->nullable();
        });
    }
}
