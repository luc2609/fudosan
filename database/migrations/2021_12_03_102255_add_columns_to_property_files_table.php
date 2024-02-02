<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPropertyFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_files', function (Blueprint $table) {
            $table->text('name');
            $table->text('url');
            $table->tinyInteger('type');
            $table->dropForeign('property_files_file_id_foreign');
            $table->dropColumn('file_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_files', function (Blueprint $table) {
        });
    }
}
