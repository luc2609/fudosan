<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCalendarsTable extends Migration
{
    public function up()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->bigInteger('root_calendar_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->dropColumn('root_calendar_id')->nullable();
        });
    }
}
