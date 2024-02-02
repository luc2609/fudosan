<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIsActionNotiToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE projects CHANGE deleted_at deleted_at timestamp AFTER reason");
        DB::statement("ALTER TABLE projects CHANGE updated_at updated_at timestamp AFTER reason");
        DB::statement("ALTER TABLE projects CHANGE created_at created_at timestamp AFTER reason");
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('is_action_noti')->default(1)->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('is_action_noti');
        });
    }
}
