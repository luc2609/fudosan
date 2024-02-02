<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAuthenticationUserTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('authentication');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->integer('authentication')->default(ACTIVE);
        });
    }

    public function down()
    {
        //
    }
}
