<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteColumnToCustomerAdvertisingForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_advertising_forms', function (Blueprint $table) {
            $table->text('note_other')->nullable()->after('advertising_form_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_advertising_forms', function (Blueprint $table) {
            $table->dropColumn('note_other');
        });
    }
}
