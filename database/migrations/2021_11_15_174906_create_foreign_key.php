<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->change();
            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->unsignedBigInteger('created_id')->change();
            $table->foreign('created_id')->references('id')->on('users');

            $table->unsignedBigInteger('approved_id')->change();
            $table->foreign('approved_id')->references('id')->on('users');

            $table->unsignedBigInteger('company_id')->change();
            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::table('property_advertising_webs', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->change();
            $table->foreign('property_id')->references('id')->on('properties');
        });

        Schema::table('property_files', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->change();
            $table->foreign('property_id')->references('id')->on('properties');

            $table->unsignedBigInteger('file_id')->change();
            $table->foreign('file_id')->references('id')->on('files');
        });

        Schema::table('property_stations', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->change();
            $table->foreign('property_id')->references('id')->on('properties');
        });

        Schema::table('user_divisions', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('division_id')->change();
            $table->foreign('division_id')->references('id')->on('divisions');
        });

        Schema::table('user_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedBigInteger('role_id')->change();
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropForeign('divisions_company_id_foreign');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign('properties_approved_id_foreign');
            $table->dropForeign('properties_company_id_foreign');
            $table->dropForeign('properties_created_id_foreign');
        });

        Schema::table('property_advertising_webs', function (Blueprint $table) {
            $table->dropForeign('property_advertising_webs_property_id_foreign');
        });

        Schema::table('property_files', function (Blueprint $table) {
            $table->dropForeign('property_files_property_id_foreign');
            $table->dropForeign('property_files_file_id_foreign');
        });

        Schema::table('property_stations', function (Blueprint $table) {
            $table->dropForeign('property_stations_property_id_foreign');
        });

        Schema::table('user_divisions', function (Blueprint $table) {
            $table->dropForeign('user_divisions_division_id_foreign');
            $table->dropForeign('user_divisions_user_id_foreign');
        });


        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropForeign('user_roles_role_id_foreign');
            $table->dropForeign('user_roles_user_id_foreign');
        });
    }
}
