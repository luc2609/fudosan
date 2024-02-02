<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('type')->comment('1: Seller, 2: Buyer')->nullable()->change();
            $table->date('dob')->nullable()->change();
            $table->boolean('sex')->nullable()->change();
            $table->string('postcode', 50)->nullable()->change();
            $table->string('image')->nullable()->change();
            $table->unsignedBigInteger('create_by_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('create_by_id');
        });
    }
}
