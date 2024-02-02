<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubCalendarIdColumnToRelateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendar_users', function (Blueprint $table) {
            $table->bigInteger('sub_calendar_id')->after('calendar_id')->nullable();
        });

        Schema::table('calendar_files', function (Blueprint $table) {
            $table->bigInteger('sub_calendar_id')->after('calendar_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calendar_users', function (Blueprint $table) {
            $table->dropColumn('sub_calendar_id');
        });

        Schema::table('calendar_files', function (Blueprint $table) {
            $table->dropColumn('sub_calendar_id');
        });
    }
}
