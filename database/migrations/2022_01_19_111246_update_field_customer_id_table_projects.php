<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldCustomerIdTableProjects extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('customer_id');
            $table->string('title')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->bigInteger('customer_id');
            $table->string('title')->change();
        });
    }
}
