<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnProjectPhaseIdTableCalendars extends Migration
{
    public function up()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->dropColumn('m_phase_project_id')->nullable();
            $table->bigInteger('project_phase_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('calendars', function (Blueprint $table) {
            $table->bigInteger('m_phase_project_id')->nullable();
            $table->dropColumn('project_phase_id')->nullable();
        });
    }
}
