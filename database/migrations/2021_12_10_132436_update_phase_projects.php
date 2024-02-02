<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePhaseProjects extends Migration
{
    public function up()
    {
        Schema::dropIfExists('project_phase_ones');
        Schema::dropIfExists('project_phase_twos');
        Schema::dropIfExists('project_phase_threes');
        Schema::dropIfExists('project_phase_fours');
        Schema::dropIfExists('project_phase_fives');
        Schema::dropIfExists('project_phase_sixs');
        Schema::dropIfExists('project_phase_sevens');
        Schema::dropIfExists('project_phase_eights');
        Schema::dropIfExists('project_phase_nines');

        Schema::create('project_phases', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id');
            $table->integer('m_phase_project_id');
            $table->dateTime('preliminary_test_date');
            $table->dateTime('actual_test_date');
            $table->integer('status');
            $table->string('created_name');
            $table->string('updated_name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('m_phase_project_id');
            $table->text('history');
            $table->bigInteger('current_phase_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_phases');

        Schema::table('projects', function (Blueprint $table) {
            $table->bigInteger('m_phase_project_id');
            $table->dropColumn('history');
            $table->dropColumn('current_phase_id');

        });
    }
}
