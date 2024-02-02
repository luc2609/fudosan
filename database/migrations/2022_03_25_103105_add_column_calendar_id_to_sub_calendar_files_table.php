<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCalendarIdToSubCalendarFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_calendar_files', function (Blueprint $table) {
            $table->bigInteger('calendar_file_id')->after('url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_calendar_files', function (Blueprint $table) {
            $table->dropColumn('calendar_file_id');
        });
    }
}
