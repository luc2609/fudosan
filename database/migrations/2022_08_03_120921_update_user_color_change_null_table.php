<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserColorChangeNullTable extends Migration
{
    public function up()
    {
        Schema::table('user_colors', function (Blueprint $table) {
            $table->text('color_web')->nullable()->change();
            $table->text('color_app')->nullable()->change();
        });
    }

    public function down()
    {
        //
    }
}
