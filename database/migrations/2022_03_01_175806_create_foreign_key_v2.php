<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForeignKeyV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_users', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('project_id')->change();
            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::table('project_customers', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->change();
            $table->foreign('project_id')->references('id')->on('projects');

            $table->unsignedBigInteger('customer_id')->change();
            $table->foreign('customer_id')->references('id')->on('customers');
        });

        Schema::table('project_advertising_webs', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->change();
            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::table('project_files', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->change();
            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::table('project_phases', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->change();
            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::table('project_purchase_purposes', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->change();
            $table->foreign('project_id')->references('id')->on('projects');

            $table->unsignedBigInteger('purchase_purpose_id')->change();
            $table->foreign('purchase_purpose_id')->references('id')->on('master_purchase_purposes');
        });

        Schema::table('calendar_users', function (Blueprint $table) {
            $table->unsignedBigInteger('calendar_id')->change();
            $table->foreign('calendar_id')->references('id')->on('calendars');

            $table->unsignedBigInteger('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('calendar_files', function (Blueprint $table) {
            $table->unsignedBigInteger('calendar_id')->change();
            $table->foreign('calendar_id')->references('id')->on('calendars');
        });

        Schema::table('sub_calendars', function (Blueprint $table) {
            $table->unsignedBigInteger('calendar_id')->change();
            $table->foreign('calendar_id')->references('id')->on('calendars');
        });

        Schema::table('sub_calendar_files', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_calendar_id')->change();
            $table->foreign('sub_calendar_id')->references('id')->on('sub_calendars');
        });

        Schema::table('sub_calendar_users', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_calendar_id')->change();
            $table->foreign('sub_calendar_id')->references('id')->on('sub_calendars');

            $table->unsignedBigInteger('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_users', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('project_customers', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['project_id']);
        });

        Schema::table('project_advertising_webs', function (Blueprint $table) {
            $table->dropForeign('project_advertising_webs_project_id_foreign');
        });

        Schema::table('project_files', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });

        Schema::table('project_phases', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });

        Schema::table('project_purchase_purposes', function (Blueprint $table) {
            $table->dropForeign(['purchase_purpose_id']);
            $table->dropForeign(['project_id']);
        });

        Schema::table('calendar_users', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['calendar_id']);
        });

        Schema::table('calendar_files', function (Blueprint $table) {
            $table->dropForeign(['calendar_id']);
        });

        Schema::table('sub_calendars', function (Blueprint $table) {
            $table->dropForeign(['calendar_id']);
        });

        Schema::table('sub_calendar_files', function (Blueprint $table) {
            $table->dropForeign(['sub_calendar_id']);
        });

        Schema::table('sub_calendar_users', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['sub_calendar_id']);
        });
    }
}
