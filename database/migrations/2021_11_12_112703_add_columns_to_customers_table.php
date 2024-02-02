<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->bigInteger('contact_method_id')->comment('1: LINE, 2: 電話, 3: メール, 4: その他')->nullable();
            $table->bigInteger('residence_year_id')->comment('1: 1年未満, 2:1～5年未満, 3:5～10年未満, 4:10～20年未満, 5:20年以上')->nullable();
            $table->bigInteger('purchase_purpose_id')->nullable();
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
            $table->dropColumn('contact_method_id');
            $table->dropColumn('residence_year_id');
            $table->dropColumn('purchase_purpose_id');
        });
    }
}
