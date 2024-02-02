<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->tinyInteger('type')->comment('1: Seller, 2: Buyer');
            $table->date('arrival_date')->nullable();
            $table->integer('life_time')->nullable();
            $table->integer('budget')->nullable();
            $table->integer('first_money')->nullable();
            $table->string('purpose_buy')->nullable();
            $table->date('time_buy')->nullable();
            $table->string('contact')->nullable();
            $table->tinyInteger('advertising_forms')->nullable();
            $table->tinyInteger('current_housing_type')->nullable()->comment('1:apartment, 2: home, 3: Villa');
            $table->tinyInteger('is_approve')->default(0)->comment('0: notApprove, 1: isApprove');
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
            $table->dropColumn('type');
            $table->dropColumn('arrival_date');
            $table->dropColumn('budget');
            $table->dropColumn('first_money');
            $table->dropColumn('purpose_buy');
            $table->dropColumn('life_time');
            $table->dropColumn('time_buy');
            $table->dropColumn('contact');
            $table->dropColumn('advertising_forms');
            $table->dropColumn('current_housing_type');
            $table->dropColumn('is_approve');
        });
    }
}
