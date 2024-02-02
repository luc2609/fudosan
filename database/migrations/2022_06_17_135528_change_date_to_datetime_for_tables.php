<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class ChangeDateToDatetimeForTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // calendars
        DB::statement('ALTER TABLE calendars MODIFY COLUMN created_at TIMESTAMP');
        DB::statement('ALTER TABLE calendars MODIFY COLUMN updated_at TIMESTAMP');
        DB::statement('ALTER TABLE calendars MODIFY COLUMN deleted_at TIMESTAMP');

        // customer
        DB::statement('ALTER TABLE customers MODIFY COLUMN created_at TIMESTAMP');
        DB::statement('ALTER TABLE customers MODIFY COLUMN updated_at TIMESTAMP');
        DB::statement('ALTER TABLE customers MODIFY COLUMN deleted_at TIMESTAMP');

        // property files
        DB::statement('ALTER TABLE property_files MODIFY COLUMN created_at TIMESTAMP');
        DB::statement('ALTER TABLE property_files MODIFY COLUMN updated_at TIMESTAMP');
        DB::statement('ALTER TABLE property_files MODIFY COLUMN deleted_at TIMESTAMP');


        // session customer
        DB::statement('ALTER TABLE session_customers MODIFY COLUMN created_at TIMESTAMP');
        DB::statement('ALTER TABLE session_customers MODIFY COLUMN updated_at TIMESTAMP');
        DB::statement('ALTER TABLE session_customers MODIFY COLUMN deleted_at TIMESTAMP');


        // user
        DB::statement('ALTER TABLE users MODIFY COLUMN created_at TIMESTAMP');
        DB::statement('ALTER TABLE users MODIFY COLUMN updated_at TIMESTAMP');
        DB::statement('ALTER TABLE users MODIFY COLUMN deleted_at TIMESTAMP');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
