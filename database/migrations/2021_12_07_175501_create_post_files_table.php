<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostFilesTable extends Migration
{
    public function up()
    {
        Schema::create('post_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('post_id');
            $table->string('url');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('post_files');
    }
}
