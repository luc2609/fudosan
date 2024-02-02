<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('company')->nullable();
            $table->tinyInteger('division')->nullable();
            $table->tinyInteger('certificate')->nullable();
            $table->string('manager')->nullable();
            $table->string('employee_code')->nullable();
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
            $table->dropColumn('company');
            $table->dropColumn('division');
            $table->dropColumn('certificate');
            $table->dropColumn('manager');
            $table->dropColumn('employee_code');
        });
    }
}
