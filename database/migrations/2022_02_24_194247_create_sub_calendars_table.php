<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_calendars', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('calendar_id');
            $table->dateTime('modify_date');
            $table->string('title');
            $table->dateTime('meeting_start_time');
            $table->dateTime('meeting_end_time');
            $table->text('meeting_url')->nullable();
            $table->string('note')->nullable();
            $table->boolean('is_deleted')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('sub_calendars');
    }
}
