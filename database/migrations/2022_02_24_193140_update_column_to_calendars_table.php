<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnToCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->dateTime('meeting_start_time')->nullable();
            $table->dateTime('meeting_end_time')->nullable();
            $table->string('repeat_day')->nullable();
            $table->dropColumn('created_id');
            $table->dropColumn('created_name');
            $table->dropColumn('root_calendar_id');
            $table->dateTime('start_date')->nullable()->change();
            $table->dateTime('end_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->dropColumn('meeting_start_time');
            $table->dropColumn('meeting_end_time');
            $table->dropColumn('repeat_day');
            $table->bigInteger('created_id');
            $table->string('created_name');
            $table->bigInteger('root_calendar_id');
        });
    }
}
