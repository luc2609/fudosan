<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateIsAcceptToCalendarUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE calendar_users CHANGE COLUMN is_accept is_accept TINYINT NULL default " . "" . UNKNOWN . "");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calendar_users', function (Blueprint $table) {
            $table->boolean('is_accept')->default(0);
        });
    }
}
