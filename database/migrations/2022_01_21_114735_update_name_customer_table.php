<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNameCustomerTable extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('furigana');
            $table->string('kana_last_name');
            $table->string('kana_first_name');
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('furigana');
            $table->dropColumn('kana_last_name');
            $table->dropColumn('kana_first_name');
        });
    }
}
