<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotifyIdRelateCalendar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->tinyInteger('notify_id')->nullable();
        });

        Schema::table('sub_calendars', function (Blueprint $table) {
            $table->tinyInteger('notify_id')->nullable();
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
            $table->dropColumn('notify_id');
        });

        Schema::table('sub_calendars', function (Blueprint $table) {
            $table->dropColumn('notify_id');
        });
    }
}
