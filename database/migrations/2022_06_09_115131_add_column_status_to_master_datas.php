<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnStatusToMasterDatas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // master advertising forms
        Schema::table('master_advertising_forms', function (Blueprint $table) {
            $table->tinyInteger('status')->after('name')->default(1);
        });

        // master advertising webs
        Schema::table('master_advertising_webs', function (Blueprint $table) {
            $table->tinyInteger('status')->after('url')->default(1);
        });

        // master brokerage fees
        Schema::table('master_brokerage_fees', function (Blueprint $table) {
            $table->tinyInteger('status')->after('max')->default(1);
        });

        // master contact methods
        Schema::table('master_contact_methods', function (Blueprint $table) {
            $table->tinyInteger('status')->after('contact_method')->default(1);
        });

        // master contact reasons
        Schema::table('master_contact_reasons', function (Blueprint $table) {
            $table->tinyInteger('status')->after('name')->default(1);
        });

        // master contact types
        Schema::table('master_contact_types', function (Blueprint $table) {
            $table->tinyInteger('status')->after('name')->default(1);
        });

        // master fields
        Schema::table('master_fields', function (Blueprint $table) {
            $table->tinyInteger('status')->after('type')->default(1);
        });

        // master notify calendars
        Schema::table('master_notify_calendars', function (Blueprint $table) {
            $table->tinyInteger('status')->after('notify')->default(1);
        });

        // master phase projects
        Schema::table('master_phase_projects', function (Blueprint $table) {
            $table->tinyInteger('status')->after('name')->default(1);
        });

        // master positions
        Schema::table('master_positions', function (Blueprint $table) {
            $table->tinyInteger('status')->after('name')->default(1);
        });

        // master postal codes
        Schema::table('master_postal_codes', function (Blueprint $table) {
            $table->tinyInteger('status')->after('street')->default(1);
        });

        // master prices
        Schema::table('master_prices', function (Blueprint $table) {
            $table->tinyInteger('status')->after('price')->default(1);
        });

        // master property building structures
        Schema::table('master_property_building_structures', function (Blueprint $table) {
            $table->tinyInteger('status')->after('name')->default(1);
        });

        // master property contract types
        Schema::table('master_property_contract_types', function (Blueprint $table) {
            $table->tinyInteger('status')->after('name')->default(1);
        });

        // master_property_current_situations
        Schema::table('master_property_current_situations', function (Blueprint $table) {
            $table->tinyInteger('status')->after('name')->default(1);
        });

        // master_property_types
        Schema::table('master_property_types', function (Blueprint $table) {
            $table->tinyInteger('status')->after('name')->default(1);
        });

        // master_provinces
        Schema::table('master_provinces', function (Blueprint $table) {
            $table->tinyInteger('status')->after('eng_name')->default(1);
        });

        // master_purchase_purposes
        Schema::table('master_purchase_purposes', function (Blueprint $table) {
            $table->tinyInteger('status')->after('purchase_purpose')->default(1);
        });

        // master_rails
        Schema::table('master_rails', function (Blueprint $table) {
            $table->tinyInteger('status')->after('name')->default(1);
        });

        // master_residence_years
        Schema::table('master_residence_years', function (Blueprint $table) {
            $table->tinyInteger('status')->after('max')->default(1);
        });

        // master_sale_purposes
        Schema::table('master_sale_purposes', function (Blueprint $table) {
            $table->tinyInteger('status')->after('sale_purpose')->default(1);
        });

        // master_schedule_repeats
        Schema::table('master_schedule_repeats', function (Blueprint $table) {
            $table->tinyInteger('status')->after('repeat')->default(1);
        });

        // master_stations
        Schema::table('master_stations', function (Blueprint $table) {
            $table->tinyInteger('status')->after('name')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // master advertising forms
        Schema::table('master_advertising_forms', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master advertising webs
        Schema::table('master_advertising_webs', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master brokerage fees
        Schema::table('master_brokerage_fees', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master contact methods
        Schema::table('master_contact_methods', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master contact reasons
        Schema::table('master_contact_reasons', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master contact types
        Schema::table('master_contact_types', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master fields
        Schema::table('master_fields', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master notify calendars
        Schema::table('master_notify_calendars', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master phase projects
        Schema::table('master_phase_projects', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master positions
        Schema::table('master_positions', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master postal codes
        Schema::table('master_postal_codes', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master prices
        Schema::table('master_prices', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master property building structures
        Schema::table('master_property_building_structures', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master property contract types
        Schema::table('master_property_contract_types', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master_property_current_situations
        Schema::table('master_property_current_situations', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master_property_types
        Schema::table('master_property_types', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master_provinces
        Schema::table('master_provinces', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master_purchase_purposes
        Schema::table('master_purchase_purposes', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master_rails
        Schema::table('master_rails', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master_residence_years
        Schema::table('master_residence_years', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master_sale_purposes
        Schema::table('master_sale_purposes', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master_schedule_repeats
        Schema::table('master_schedule_repeats', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // master_stations
        Schema::table('master_stations', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
