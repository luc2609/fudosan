<?php

use Illuminate\Http\Request;
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
| API Version 2
|
*/


Route::group(['namespace' => 'Api\v1'], function () {
    // test noti
    Route::post('test', 'TestNotiController@testNoti');

    // login Auth
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
        Route::get('profile', 'AuthController@profile');
        Route::post('profile', 'AuthController@update');
        Route::post('password', 'AuthController@updatePassword');
        Route::post('password/verify_token', 'AuthController@verifyTokenUpdatePassword');

        //customer v2
        Route::post('customers/check_duplicate', 'CustomerController@checkDuplicate')
            ->middleware('permission:show_customer');
        Route::post('customers', 'CustomerController@store')
            ->middleware('permission:create_customer');
        Route::get('customers', 'CustomerController@listCustomer')
            ->middleware('permission:show_customer');
        Route::get('customers/{id}', 'CustomerController@show')
            ->middleware('permission:show_customer')->where('id', '[0-9]+');
    });
});
