<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumCloseStatusTableProjects extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->tinyInteger('close_status')->default(IN_PROGRESS);
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('close_status');
        });
    }
}
