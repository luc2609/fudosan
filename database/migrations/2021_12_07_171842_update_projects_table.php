<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProjectsTable extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('user_in_charge_id');
            $table->dropColumn('sub_user_in_charge_id')->nullable();
            $table->double('revenue')->nullable();
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->bigInteger('user_in_charge_id');
            $table->bigInteger('sub_user_in_charge_id')->nullable();
            $table->dropColumn('revenue')->nullable();
        });
    }
}
