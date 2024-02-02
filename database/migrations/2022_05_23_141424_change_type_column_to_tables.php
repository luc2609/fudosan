<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeTypeColumnToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // calendar user
        DB::statement("ALTER TABLE calendar_users CHANGE COLUMN is_host is_host TINYINT");

        // company
        Schema::table('companies', function (Blueprint $table) {
            $table->text('note')->change();
            $table->integer('commission_rate')->change();
        });

        // customer
        Schema::table('customers', function (Blueprint $table) {
            $table->string('kana_last_name')->change();
            $table->string('kana_first_name')->change();
            $table->string('last_name')->change();
            $table->string('first_name')->change();
        });


        // post
        Schema::table('posts', function (Blueprint $table) {
            $table->text('title')->change();
        });

        // project file
        Schema::table('project_files', function (Blueprint $table) {
            $table->text('url')->change();
            $table->text('name')->change();
        });

        // project user
        DB::statement("ALTER TABLE project_users MODIFY brokerage_fee DOUBLE");

        // sub calendar
        DB::statement("ALTER TABLE sub_calendars CHANGE COLUMN notify_id notify_id TINYINT");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
}
