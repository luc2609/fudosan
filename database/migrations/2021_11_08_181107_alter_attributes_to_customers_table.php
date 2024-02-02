<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterAttributesToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('first_name', 'username');
            $table->renameColumn('first_name_kana', 'furigana');
            $table->renameColumn('is_approve', 'status');
            $table->date('birthday')->nullable(false)->change();
            $table->boolean('gender')->nullable(false)->change();
            $table->string('postal_code', 50)->nullable(false)->change();
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
            $table->renameColumn('username', 'first_name');
            $table->renameColumn('furigana', 'first_name_kana');
            $table->renameColumn('status', 'is_approve');
        });
    }
}
