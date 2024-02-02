<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('created_id');
            $table->string('created_name');
            $table->string('title');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->bigInteger('project_id')->nullable();
            $table->bigInteger('m_phase_project_id')->nullable();
            $table->bigInteger('company_id')->nullable();
            $table->bigInteger('division_id')->nullable();
            $table->text('meeting_url')->nullable();
            $table->string('note')->nullable();
            $table->bigInteger('repeat_id')->nullable();
            $table->tinyInteger('meeting_type')->comment('1: internal, 2: customer meeting');
            $table->boolean('is_public')->comment('1: public, 0: private');
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
        Schema::dropIfExists('calendars');
    }
}
