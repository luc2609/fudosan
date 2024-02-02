<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDataSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->bigInteger('create_by_id');
            $table->string('title');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->bigInteger('project_id')->nullable();
            $table->bigInteger('property_id')->nullable();
            $table->bigInteger('customer_id')->nullable();
            $table->text('meeting_url')->nullable();
            $table->string('note')->nullable();
            $table->bigInteger('repeat_id');
            $table->tinyInteger('meeting_type')->comment('1: internal, 2: customer meeting');
            $table->tinyInteger('is_notif')->nullable()->comment('1: ON, 2:OFF');
            $table->tinyInteger('public_status')->comment('1: public, 2: private');
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
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('create_by_id');
            $table->dropColumn('title');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('project_id');
            $table->dropColumn('property_id');
            $table->dropColumn('customer_id');
            $table->dropColumn('meeting_url');
            $table->dropColumn('note');
            $table->dropColumn('repeat_id');
            $table->dropColumn('meeting_type');
            $table->dropColumn('is_notif');
            $table->dropColumn('public_status');
        });
    }
}
