<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropColumnCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['last_name', 'last_name_kana', 'image', 'visit_date', 'customer_type', 'residence_type', 'advertising_forms']);
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
            $table->string('last_name');
            $table->string('last_name_kana');
            $table->string('image');
            $table->date('visit_date');
            $table->tinyInteger('customer_type');
            $table->tinyInteger('residence_type');
            $table->tinyInteger('advertising_forms');
        });
    }
}
