<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('furigana');
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('first_name_kata');
            $table->dropColumn('last_name_kata');
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
            $table->dropColumn('furigana');
            $table->string('last_name');
            $table->string('first_name');
            $table->string('first_name_kata');
            $table->string('last_name_kata');
        });
    }
}
