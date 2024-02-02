<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserColorsTable extends Migration
{
    public function up()
    {
        Schema::create('user_colors', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->text('color');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_colors');
    }
}
