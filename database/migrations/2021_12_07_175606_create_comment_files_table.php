<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentFilesTable extends Migration
{
    public function up()
    {
        Schema::create('comment_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('comment_id');
            $table->string('url');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('comment_files');
    }
}
