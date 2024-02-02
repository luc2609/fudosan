<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectFilesTable extends Migration
{
    public function up()
    {
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('project_id');
            $table->bigInteger('file_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_files');
    }
}
