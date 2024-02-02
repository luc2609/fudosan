<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeAttributeColumnCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('residence_years')->change();
            $table->string('memo')->nullable()->change();
        });
        DB::statement("ALTER TABLE customers CHANGE COLUMN budget budget DOUBLE NULL");
        DB::statement("ALTER TABLE customers CHANGE COLUMN deposit deposit DOUBLE NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('budget')->change();
            $table->integer('deposit')->change();
            $table->bigInteger('residence_years')->change();
        });
    }
}
