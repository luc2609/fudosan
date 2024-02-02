<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDeletedAtSubCalendarUsersTables extends Migration
{
    public function up()
    {
        Schema::table('sub_calendar_users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
    }
}
