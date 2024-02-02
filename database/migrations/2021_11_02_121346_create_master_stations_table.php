<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_stations', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('province_cd');
            $table->mediumInteger('rail_cd');
            $table->mediumInteger('cd');
            $table->string('name', 21);
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
        Schema::dropIfExists('master_stations');
    }
}
