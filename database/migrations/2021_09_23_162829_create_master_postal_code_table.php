<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterPostalCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_postal_code', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('city_code', 10);
            $table->string('postal_code', 10);
            $table->string('kana_province');
            $table->string('kana_district');
            $table->string('kana_street');
            $table->string('province');
            $table->string('district');
            $table->string('street');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_postal_code');
    }
}
