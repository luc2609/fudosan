<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameAndFuriganaToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn('furigana');
            $table->string('kana_last_name')->after('id');
            $table->string('kana_first_name')->after('id');
            $table->string('last_name')->after('id');
            $table->string('first_name')->after('id');
            $table->renameColumn('sex', 'gender');
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
            $table->string('furigana')->after('id');
            $table->string('username')->after('id');
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('kana_first_name');
            $table->dropColumn('kana_last_name');
            $table->renameColumn('gender', 'sex');
        });
    }
}
