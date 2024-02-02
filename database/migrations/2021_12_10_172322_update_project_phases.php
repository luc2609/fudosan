<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateProjectPhases extends Migration
{
    public function up()
    {
        Schema::dropIfExists('project_phases');

        Schema::create('project_phases', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id');
            $table->integer('m_phase_project_id');
            $table->dateTime('preliminary_test_date')->nullable();
            $table->dateTime('actual_test_date')->nullable();
            $table->integer('status');
            $table->string('created_name');
            $table->string('updated_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_phases');
    }
}
