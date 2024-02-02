<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('first_name_kata', 'first_name_kana');
            $table->renameColumn('last_name_kata', 'last_name_kana');
            $table->renameColumn('sex', 'gender');
            $table->renameColumn('dob', 'birthday');
            $table->renameColumn('postcode', 'postal_code');
            $table->renameColumn('type', 'customer_type');
            $table->renameColumn('current_housing_type', 'residence_type');
            $table->renameColumn('life_time', 'residence_years');
            $table->renameColumn('contact', 'contact_method');
            $table->renameColumn('arrival_date', 'visit_date');
            $table->renameColumn('first_money', 'deposit');
            $table->renameColumn('purpose_buy', 'purchase_purpose');
            $table->renameColumn('time_buy', 'purchase_time');
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
            $table->renameColumn('first_name_kana', 'first_name_kata');
            $table->renameColumn('last_name_kana', 'last_name_kata');
            $table->renameColumn('gender', 'sex');
            $table->renameColumn('birthday', 'dob');
            $table->renameColumn('postal_code', 'postcode');
            $table->renameColumn('customer_type', 'type');
            $table->renameColumn('residence_type', 'current_housing_type');
            $table->renameColumn('residence_years', 'life_time');
            $table->renameColumn('contact_method', 'contact');
            $table->renameColumn('visit_date', 'arrival_date');
            $table->renameColumn('deposit', 'first_money');
            $table->renameColumn('purchase_purpose', 'purpose_buy');
            $table->renameColumn('purchase_time', 'time_buy');
        });
    }
}
