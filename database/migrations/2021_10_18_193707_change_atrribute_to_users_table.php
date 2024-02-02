<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAtrributeToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 50)->nullable()->change();
            $table->date('dob')->nullable()->change();
            $table->boolean('sex')->nullable()->change();
            $table->string('postcode', 50)->nullable()->change();
            $table->string('avatar')->nullable()->change();
            $table->boolean('status')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->boolean('flag')->nullable()->change();
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('first_name_kata')->nullable()->change();
            $table->string('last_name_kata')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
