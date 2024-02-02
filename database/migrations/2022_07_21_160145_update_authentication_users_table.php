<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAuthenticationUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('close_project_date')->nullable();
            $table->integer('authentication')->config(ACTIVE);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('close_project_date')->nullable();
            $table->dropColumn('authentication')->config(ACTIVE);
        });
    }
}
