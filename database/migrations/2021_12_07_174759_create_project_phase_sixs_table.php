<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectPhaseSixsTable extends Migration
{
    public function up()
    {
        Schema::create('project_phase_sixs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('project_id');
            $table->integer('status');
            $table->text('history');
            $table->string('created_name');
            $table->string('updated_name');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_phase_sixs');
    }
}
