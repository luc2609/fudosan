<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBirthdayUsersTable extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->date('birthday')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->date('birthday')->change();
        });
    }
}
