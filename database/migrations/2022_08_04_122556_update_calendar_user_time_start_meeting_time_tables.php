<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCalendarUserTimeStartMeetingTimeTables extends Migration
{
    public function up()
    {
        Schema::table('calendar_users', function (Blueprint $table) {
            $table->dateTime('start_time_meeting')->nullable();
        });
    }

    public function down()
    {
        Schema::table('calendar_users', function (Blueprint $table) {
            $table->dropColumn('start_time_meeting')->nullable();
        });
    }
}
