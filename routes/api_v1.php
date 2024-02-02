<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
| API Version 1
|
*/


Route::namespace('Api\v1')->group(function () {
    // Route::get('test', 'ProjectController@test');
    Route::get('test', 'CalendarController@test');
    Route::get('session_customers/{token}', 'CustomerController@getSessionCustomer');

    // login
    Route::post('login', 'AuthController@login');
    Route::post('login/verify_token', 'AuthController@verifyToken');
    Route::post('login/resend_token', 'AuthController@resendToken');
    Route::post('reset_password/send_token', 'AuthController@sendTokenResetPassword');
    Route::post('reset_password/verify_token', 'AuthController@verifyTokenResetPassword');
    Route::post('reset_password', 'AuthController@resetPassword');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', 'AuthController@logout');
        Route::post('device_tokens', 'AuthController@createDeviceToken');
        Route::post('push_notify_enable', 'AuthController@pushNotifyEnable');
        Route::post('email_notify_enable', 'AuthController@emailNotifyEnable');
        Route::post('security_enable', 'AuthController@securityEnable');
        Route::get('configuration', 'AuthController@configuration');
        Route::post('password', 'AuthController@updatePassword');
        Route::post('password/verify_token', 'AuthController@verifyTokenUpdatePassword');
        Route::get('profile', 'AuthController@profile');
        Route::get('commission', 'AuthController@userCommission');

        //change 
        Route::put('users/authentication', 'UserController@changeAuthenticationCms');

        // user logout
        Route::put('admins/log_out/users/{id}', 'AuthController@adminLogoutUser')
            ->middleware('permission:update_account_cms_company');

        Route::get('master_positions', 'MasterPositionController@index');
        Route::get('master_postal_code/{postal_code}', 'MasterDataController@address');
        Route::get('master_postal_code', 'MasterDataController@indexPostalCode');
        Route::get('master_property_contract_types', 'MasterDataController@indexPropertyContractType');
        Route::get('master_property_current_situations', 'MasterDataController@indexPropertyCurrentSituation');
        Route::get('master_property_types', 'MasterDataController@indexPropertyType');
        Route::get('master_property_building_structures', 'MasterDataController@indexPropertyBuildingStructure');
        Route::get('master_advertising_webs', 'MasterDataController@indexAdvertisingWeb');
        Route::get('master_provinces', 'MasterDataController@indexProvince');
        Route::get('master_districts', 'MasterDataController@indexDistrict');
        Route::get('master_rails', 'MasterDataController@indexRail');
        Route::get('master_prices', 'MasterDataController@indexPrice');
        Route::get('master_brokerage_fees', 'MasterDataController@indexBrokerageFee');
        Route::get('master_residence_years', 'MasterDataController@indexResidenceYears');
        Route::get('master_contact_methods', 'MasterDataController@indexContactMethods');
        Route::get('master_purchase_purposes', 'MasterDataController@indexPurchasePurposes');
        Route::get('master_schedule_repeats', 'MasterDataController@indexScheduleRepeats');
        Route::get('master_phase_projects', 'MasterDataController@indexPhaseProject');
        Route::get('master_advertising_forms', 'MasterDataController@indexAdvertisingForms');
        Route::get('master_sale_purposes', 'MasterDataController@indexSalePurposes');
        Route::get('master_notify_calendars', 'MasterDataController@indexNotifyCalendars');
        Route::get('master_contact_reason', 'MasterDataController@indexContactReason');
        Route::get('master_fields', 'MasterDataController@indexMasterField');
        Route::get('master_roles', 'MasterDataController@indexRole');
        Route::post('users/colors', 'UserController@createUserColor');

        // delete and update master data in cms_system
        Route::delete('master_datas/{type_data}/{id}', 'MasterDataController@deleteMasterData')
            ->middleware('permission:update_company');
        Route::post('master_datas/{type_data}/{id}', 'MasterDataController@editMasterData')
            ->middleware('permission:update_company');
        Route::get('master_datas/{type_data}/{id}', 'MasterDataController@showMasterData')
            ->middleware('permission:update_company');
        Route::post('master_datas/{type_data}', 'MasterDataController@createMasterData')
            ->middleware('permission:update_company');


        Route::get('available_divisions', 'DivisionController@indexListDivision')
            ->middleware('permission:show_division');

        Route::get('brokerage_fee', 'PropertyController@showBrokerageFee')
            ->middleware('permission:create_property');

        Route::get('qr_code/{token}', 'CustomerController@qrCode')
            ->middleware('permission:create_customer');

        Route::get('my-company', 'CompanyController@detail')
            ->middleware('permission:show_company');

        Route::get('company_users', 'UserController@indexCompanyUser')
            ->middleware('permission:show_employee');

        Route::get('division_users', 'UserController@indexDivisionUser')
            ->middleware('permission:show_employee');

        Route::post('approved_calendars/{id}', 'CalendarController@approvedCalendar')
            ->middleware('permission:update_calendar')->where('id', '[0-9]+');

        //Send email contact us
        Route::post('contacts', 'ContactController@createContact');
        Route::get('contacts', 'ContactController@index')
            ->middleware('permission:show_contact');
        Route::get('contacts/{id}', 'ContactController@show')
            ->middleware('permission:show_contact');
        Route::delete('contacts/{id}', 'ContactController@destroy')
            ->middleware('permission:delete_contact');
        //Total Notifications
        Route::get('total_notifies', 'UserController@totalNotifies');
    });
});

Route::prefix('divisions')->namespace('Api\v1')->group(function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('', 'DivisionController@index')
            ->middleware('permission:show_division');
        Route::post('', 'DivisionController@store')
            ->middleware('permission:create_division');
        Route::put('{id}', 'DivisionController@update')
            ->middleware('permission:update_division');
        Route::delete('{id}', 'DivisionController@destroy')
            ->middleware('permission:delete_division');
        Route::get('export', 'DivisionController@export')
            ->middleware('permission:show_division');
        Route::get('{id}/managers', 'DivisionController@showManagersOfDivision')
            ->middleware('permission:show_division');
        Route::get('{id}/users', 'DivisionController@showUsersOfDivision')
            ->middleware('permission:show_division');
        Route::get('{id}/manager_users', 'DivisionController@showEmployeeOfDivison')
            ->middleware('permission:show_division');
        Route::get('{id}/available_managers', 'DivisionController@showAvailableManagers')
            ->middleware('permission:update_manager_division');
        Route::post('{id}/available_managers', 'DivisionController@addAvailableManagers')
            ->middleware('permission:update_manager_division');
        Route::delete('{id}/available_managers', 'DivisionController@destroyAvailableManagers')
            ->middleware('permission:update_manager_division');
        Route::get('{id}/available_users', 'DivisionController@showAvailableUsers')
            ->middleware('permission:update_user_division');
        Route::post('{id}/available_users', 'DivisionController@addAvailableUsers')
            ->middleware('permission:update_user_division');
        Route::delete('{id}/available_users', 'DivisionController@destroyAvailableUsers')
            ->middleware('permission:delete_user_division');
        Route::get('{id}/projects', 'DivisionController@indexProject')
            ->middleware('permission:show_division');
    });
});

// properties
Route::prefix('properties')->namespace('Api\v1')->group(function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('', 'PropertyController@index')
            ->middleware('permission:show_property');
        Route::get('total', 'PropertyController@getTotalProperty');
        Route::get('export', 'PropertyController@export')
            ->middleware('permission:show_property');
        Route::post('check_duplicate', 'PropertyController@checkDuplicate')
            ->middleware('permission:show_property');
        Route::post('', 'PropertyController@store')
            ->middleware('permission:create_property');
        Route::post('{id}', 'PropertyController@update')
            ->middleware('permission:update_property');
        Route::get('{id}', 'PropertyController@show')
            ->middleware('permission:show_property');
        Route::delete('{id}', 'PropertyController@destroy')
            ->middleware('permission:delete_property');
        Route::get('{id}/projects', 'PropertyController@indexProject')
            ->middleware('permission:show_property');
    });
});

// customers
Route::prefix('customers')->namespace('Api\v1')->group(function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('check_duplicate', 'CustomerController@checkDuplicate')
            ->middleware('permission:show_customer');
        Route::post('', 'CustomerController@store')
            ->middleware('permission:create_customer');
        Route::get('', 'CustomerController@index')
            ->middleware('permission:show_customer');
        Route::delete('{id}', 'CustomerController@destroy')
            ->middleware('permission:delete_customer')->where('id', '[0-9]+');
        Route::get('export', 'CustomerController@export')
            ->middleware('permission:show_customer');
        Route::get('{id}', 'CustomerController@show')
            ->middleware('permission:show_customer')->where('id', '[0-9]+');
        Route::put('{id}', 'CustomerController@update')
            ->middleware('permission:update_customer')->where('id', '[0-9]+');
        Route::get('{id}/projects', 'CustomerController@indexProject')
            ->middleware('permission:show_customer');
    });
});

// users
Route::prefix('users')->namespace('Api\v1')->group(function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('roles/{role_id}', 'UserController@store')
            ->middleware('permission:create_employee')->where('role_id', '[0-9]+');
        Route::get('roles/{role_id}', 'UserController@index')
            ->middleware('permission:show_employee')->where('role_id', '[0-9]+');
        Route::get('export/{role_id}', 'UserController@export')
            ->middleware('permission:show_employee')->where('role_id', '[0-9]+');
        Route::get('{id}', 'UserController@show')
            ->middleware('permission:show_employee')->where('id', '[0-9]+');
        Route::delete('{id}', 'UserController@destroy')
            ->middleware('permission:delete_employee')->where('id', '[0-9]+');
        Route::put('{id}', 'UserController@update')
            ->middleware('permission:update_employee')->where('id', '[0-9]+');
        Route::post('update_avatar/{id}', 'UserController@updateAvatar')
            ->middleware('permission:update_employee');
        Route::post('{id}/add_available_divisions', 'UserController@addAvailableDivisions')
            ->middleware('permission:update_employee');
        Route::get('{id}/available_divisions', 'UserController@getAddAvailableDivisions')
            ->middleware('permission:show_employee');
        Route::get('{id}/divisions', 'UserController@showDivisions')
            ->middleware('permission:show_employee');
        Route::delete('{id}/available_divisions/{division_id}', 'UserController@destroyAvailableDivisions')
            ->middleware('permission:update_employee');
        Route::get('{id}/projects', 'UserController@indexProject')
            ->middleware('permission:show_employee');

        // api merge manager and staff
        Route::get('exports', 'UserController@exportEmployee')
            ->middleware('permission:show_employee');
        Route::post('', 'UserController@createEmployee')
            ->middleware('permission:create_employee');
    });
});

// Certificate
Route::namespace('Api\v1')->group(function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('{user_id}/certificate/app', 'CertificateController@curdCertificateApp')
            ->middleware('permission:update_employee');
        Route::post('{user_id}/certificates', 'CertificateController@store')
            ->middleware('permission:update_employee');
        Route::post('certificates/{id}', 'CertificateController@update')
            ->middleware('permission:update_employee');
        Route::delete('certificates/{id}', 'CertificateController@destroy')
            ->middleware('permission:delete_employee');
        Route::get('{user_id}/certificates', 'CertificateController@index')
            ->middleware('permission:show_employee');
        Route::get('certificates/{id}', 'CertificateController@show')
            ->middleware('permission:show_employee');
    });
});

// companies
Route::prefix('companies')->namespace('Api\v1')->group(function () {
    Route::group(['middleware' => 'auth:api'], function () {
        // account company
        Route::post('admins', 'CompanyController@addAccountAdminCmsCompany')
            ->middleware('permission:create_account_cms_company');
        Route::put('admins/{id}', 'CompanyController@updateAccountCmsCompany')
            ->middleware('permission:update_account_cms_company');
        Route::delete('admins/{id}', 'CompanyController@deleteAccountCmsCompany')
            ->middleware('permission:delete_account_cms_company');
        Route::get('admins', 'CompanyController@getListAccountCmsCompany')
            ->middleware('permission:show_account_cms_company');
        Route::get('admins/{id}', 'CompanyController@showAccountCmsCompany')
            ->middleware('permission:show_account_cms_company');

        // Custom field
        Route::post('custom_fields', 'CompanyController@curdCustomField')
            ->middleware('permission:create_custom_field');
        Route::get('custom_fields/{pattern_type}', 'CompanyController@getCustomField')
            ->middleware('permission:show_custom_field');

        // company

        Route::get('dashboard_total', 'CompanyController@getDashboardTotal');
        Route::get('detail', 'CompanyController@getCompanyDetail');
        Route::post('', 'CompanyController@store')
            ->middleware('permission:create_company');
        Route::post('{id}', 'CompanyController@update')
            ->middleware('permission:update_company');
        Route::delete('{id}', 'CompanyController@destroy')
            ->middleware('permission:delete_company');
        Route::get('', 'CompanyController@index')
            ->middleware('permission:show_company');
        Route::get('{id}', 'CompanyController@show')
            ->middleware('permission:show_cms_company');

        Route::get('cms_system/export', 'CompanyController@export')
            ->middleware('permission:show_company');

        Route::get('cms_system/{id}/admins', 'CompanyController@detailAdminCompanySystem')
            ->middleware('permission:show_company')->where('id', '[0-9]+');
        Route::get('cms_system/{id}/divisions', 'CompanyController@detailDivisionCompanySystem')
            ->middleware('permission:show_company')->where('id', '[0-9]+');

        // division
        Route::delete('divisions/{division_id}', 'CompanyController@destroyDivision')
            ->middleware('permission:update_company');
    });
});

// projects
Route::prefix('projects')->namespace('Api\v1')->group(function () {
    Route::group(['middleware' => 'auth:api'], function () {
        // posts
        Route::get('{id}/posts', 'ProjectController@showReportProject')
            ->middleware('permission:show_project');
        Route::post('{id}/posts', 'ProjectController@createReport')
            ->middleware('permission:create_report');
        Route::put('{id}/posts/{postId}', 'ProjectController@updateReport')
            ->middleware('permission:update_report');
        Route::delete('{id}/posts/{postId}', 'ProjectController@deletePost')
            ->middleware('permission:delete_project');
        // comment
        Route::post('{id}/posts/{postId}/comments', 'ProjectController@createComment')
            ->middleware('permission:create_report');
        Route::put('{id}/posts/{postId}/comments/{commentId}', 'ProjectController@updateComment')
            ->middleware('permission:update_report');
        Route::delete('{id}/posts/{postId}/comments/{commentId}', 'ProjectController@deleteComment')
            ->middleware('permission:update_report');
        // project
        Route::get('count_phases', 'ProjectPhaseController@getCountPhase');
        Route::get('request_closes', 'ProjectController@indexRequestClose')
            ->middleware('permission:show_project');
        Route::get('', 'ProjectController@index')
            ->middleware('permission:show_project');
        Route::get('{id}', 'ProjectController@show')
            ->middleware('permission:show_project');
        Route::get('{id}/history', 'ProjectController@showHistory')
            ->middleware('permission:show_project');
        Route::post('', 'ProjectController@store')
            ->middleware('permission:create_project');
        Route::post('{id}', 'ProjectController@update')
            ->middleware('permission:update_project');
        Route::delete('{id}', 'ProjectController@destroy')
            ->middleware('permission:delete_project');
        Route::post('{id}/close', 'ProjectController@updateClose')
            ->middleware('permission:update_project');
        Route::get('{id}/phases', 'ProjectPhaseController@showPhaseProject')
            ->middleware('permission:show_project');
        Route::post('{project_id}/phases/{id}', 'ProjectPhaseController@updatePhaseProject')
            ->middleware('permission:update_project');

        Route::post('update_phases/{id}', 'ProjectController@updatePhase')
            ->middleware('permission:update_project');

        //cancel 
        Route::put('{is}/cancel', 'ProjectController@cancelProject')
            ->middleware('permission:create_project');
    });
});

// calendars
Route::prefix('calendars')->namespace('Api\v1')->group(function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('', 'CalendarController@store')
            ->middleware('permission:create_calendar');
        Route::get('check_exists', 'CalendarController@checkExistCalendar')
            ->middleware('permission:create_calendar');
        Route::post('{id}', 'CalendarController@update')
            ->middleware('permission:update_calendar')->where('id', '[0-9]+');
        Route::get('', 'CalendarController@index')
            ->middleware('permission:show_calendar');
        Route::delete('{id}', 'CalendarController@destroy')
            ->middleware('permission:delete_calendar')->where('id', '[0-9]+');
        Route::get('{id}', 'CalendarController@show')
            ->middleware('permission:show_calendar')->where('id', '[0-9]+');
        Route::put('{id}/change_noti', 'CalendarController@changeNotiCalendar')
            ->middleware('permission:show_calendar')->where('id', '[0-9]+');
    });
});

// rankings
Route::prefix('rankings')->namespace('Api\v1')->group(function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('{type_ranking}', 'RankingController@index')->where('type_ranking', '[0-9]+');
        Route::get('divisions', 'RankingController@indexDivision');
        Route::get('users', 'RankingController@indexUser');
    });
});

// import data
Route::prefix('imports')->namespace('Api\v1')->group(function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('users', 'UserController@importEmployee')
            ->middleware('permission:import_user');
        Route::post('divisions', 'DivisionController@importDivision')
            ->middleware('permission:import_division');
        Route::post('customers', 'CustomerController@importCustomer')
            ->middleware('permission:import_customer');
        Route::post('properties', 'PropertyController@importProperty')
            ->middleware('permission:import_property');
    });
});
