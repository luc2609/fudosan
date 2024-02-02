<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSessionCustomerLasrFirstNameTable extends Migration
{
    public function up()
    {
        Schema::table('session_customers', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->string('last_name');
            $table->string('first_name');
        });
    }


    public function down()
    {
        Schema::table('session_customers', function (Blueprint $table) {
            $table->string('username');
            $table->dropColumn('last_name');
            $table->dropColumn('first_name');
        });
    }
}
