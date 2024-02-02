<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColorAppUserColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_colors', function (Blueprint $table) {
            $table->dropColumn('color');
            $table->text('color_web');
            $table->text('color_app');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_colors', function (Blueprint $table) {
            $table->text('color');
            $table->dropColumn('color_web');
            $table->dropColumn('color_app');
        });
    }
}
