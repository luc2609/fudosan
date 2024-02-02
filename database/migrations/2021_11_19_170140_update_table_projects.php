<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableProjects extends Migration
{
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('title');
            $table->bigInteger('user_in_charge_id');
            $table->bigInteger('sub_user_in_charge_id')->nullable();
            $table->bigInteger('customer_id');
            $table->bigInteger('property_id');
            $table->bigInteger('division_id');
            $table->bigInteger('company_id');
            $table->double('price')->nullable();
            $table->double('deposit_price')->nullable();
            $table->double('monthly_price')->nullable();
            $table->string('purpose')->nullable();
            $table->dateTime('transaction_time')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('type');
            $table->bigInteger('m_pharse_project_id');
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('user_in_charge_id');
            $table->dropColumn('sub_user_in_charge_id');
            $table->dropColumn('customer_id');
            $table->dropColumn('property_id');
            $table->dropColumn('division_id');
            $table->dropColumn('company_id');
            $table->dropColumn('price');
            $table->dropColumn('deposit_price');
            $table->dropColumn('monthly_price');
            $table->dropColumn('purpose');
            $table->dropColumn('transaction_time');
            $table->dropColumn('description');
            $table->dropColumn('type');
            $table->dropColumn('m_pharse_project_id');
        });
    }
}
