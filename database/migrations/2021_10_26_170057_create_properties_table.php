<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->text('avatar');
            $table->string('name');
            $table->date('construction_date');
            $table->string('postal_code', 15);
            $table->string('prefectures');
            $table->string('municipalities');
            $table->text('address');
            $table->double('price');
            $table->double('brokerage_fees');
            $table->tinyInteger('contract_type_id');
            $table->tinyInteger('current_situation_id');
            $table->tinyInteger('properties_type_id');
            $table->double('land_area')->nullable();
            $table->double('total_floor_area')->nullable();
            $table->double('usage_ratio')->nullable();
            $table->double('empty_ratio')->nullable();
            $table->integer('floor')->nullable();
            $table->tinyInteger('building_structure');
            $table->string('design')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status'); // wait, approved, reject
            $table->bigInteger('created_id');
            $table->bigInteger('approved_id')->nullable();
            $table->bigInteger('company_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
