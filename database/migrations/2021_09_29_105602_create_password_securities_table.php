<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordSecuritiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_securities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->boolean('security_enable')->default(true);
            $table->string('token')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('incorrect')->default(0);
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
        Schema::dropIfExists('password_securities');
    }
}
