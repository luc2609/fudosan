<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubCalendarUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_calendar_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sub_calendar_id');
            $table->bigInteger('user_id');
            $table->tinyInteger('notify_id')->nullable();
            $table->boolean('is_accept')->default(0);
            $table->boolean('is_host')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_calendar_users');
    }
}
