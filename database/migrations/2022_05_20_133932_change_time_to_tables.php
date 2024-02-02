<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ChangeTimeToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // calendars
        DB::statement("ALTER TABLE calendars MODIFY COLUMN deleted_at DATE AFTER notify_id");
        DB::statement("ALTER TABLE calendars MODIFY COLUMN updated_at DATE AFTER notify_id");
        DB::statement("ALTER TABLE calendars MODIFY COLUMN created_at DATE AFTER notify_id");

        // calendar user
        DB::statement("ALTER TABLE calendar_users MODIFY COLUMN is_host DATE AFTER is_accept");

        // company
        DB::statement("ALTER TABLE companies MODIFY COLUMN note DATE AFTER website");
        DB::statement("ALTER TABLE companies MODIFY COLUMN commission_rate DATE AFTER website");

        // customer
        DB::statement("ALTER TABLE customers MODIFY COLUMN kana_last_name DATE AFTER id");
        DB::statement("ALTER TABLE customers MODIFY COLUMN kana_first_name DATE AFTER id");
        DB::statement("ALTER TABLE customers MODIFY COLUMN last_name DATE AFTER id");
        DB::statement("ALTER TABLE customers MODIFY COLUMN first_name DATE AFTER id");
        DB::statement("ALTER TABLE customers MODIFY COLUMN deleted_at DATE AFTER reason_reject");
        DB::statement("ALTER TABLE customers MODIFY COLUMN updated_at DATE AFTER reason_reject");
        DB::statement("ALTER TABLE customers MODIFY COLUMN created_at DATE AFTER reason_reject");

        // post
        DB::statement("ALTER TABLE posts MODIFY COLUMN title DATE AFTER id");

        // project file
        DB::statement("ALTER TABLE project_files MODIFY COLUMN url DATE AFTER project_id");
        DB::statement("ALTER TABLE project_files MODIFY COLUMN name DATE AFTER project_id");

        // project user
        DB::statement("ALTER TABLE project_users MODIFY COLUMN brokerage_fee  DATE AFTER user_type");

        // property files
        DB::statement("ALTER TABLE property_files MODIFY COLUMN deleted_at  DATE AFTER type");
        DB::statement("ALTER TABLE property_files MODIFY COLUMN updated_at  DATE AFTER type");
        DB::statement("ALTER TABLE property_files MODIFY COLUMN created_at  DATE AFTER type");

        // session customer
        DB::statement("ALTER TABLE session_customers MODIFY COLUMN deleted_at  DATE AFTER bearer_token");
        DB::statement("ALTER TABLE session_customers MODIFY COLUMN updated_at  DATE AFTER bearer_token");
        DB::statement("ALTER TABLE session_customers MODIFY COLUMN created_at  DATE AFTER bearer_token");

        // sub calendar
        DB::statement("ALTER TABLE sub_calendars MODIFY COLUMN notify_id  DATE AFTER is_deleted");

        // user
        DB::statement("ALTER TABLE users MODIFY COLUMN deleted_at  DATE AFTER commission_rate");
        DB::statement("ALTER TABLE users MODIFY COLUMN updated_at  DATE AFTER commission_rate");
        DB::statement("ALTER TABLE users MODIFY COLUMN created_at  DATE AFTER commission_rate");
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
