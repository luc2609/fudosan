<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableCompanies extends Migration
{
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('province')->nullable()->change();
            $table->string('district')->nullable()->change();
            $table->string('street')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('phone', 50)->nullable()->change();
            $table->string('website')->nullable()->change();
            $table->integer('commission_rate')->nullable();
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('province')->change();
            $table->string('district')->change();
            $table->string('street')->change();
            $table->string('address')->change();
            $table->string('phone', 50)->change();
            $table->string('website')->change();
            $table->dropColumn('commission_rate')->nullable();
        });
    }
}
