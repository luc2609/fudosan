<?php

define('SYSTEM_NAME', 'FDK');

// HTTP Status code
define('HTTP_SUCCESS', 200);
define('HTTP_CREATED', 201);
define('HTTP_ACCEPTED', 202);
define('HTTP_NO_CONTENT', 204);
define('HTTP_RESET_CONTENT', 205);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_PAYMENT_REQUIRED', 402);
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_METHOD_NOT_ALLOWED', 405);
define('HTTP_NOT_ACCEPTABLE', 406);
define('HTTP_REQUEST_TIMEOUT', 408);
define('HTTP_INTERNAL_SERVER_ERROR', 500);
define('HTTP_NOT_IMPLEMENTED', 501);
define('HTTP_BAD_GATEWAY', 502);
define('HTTP_SERVICE_UNAVAILABLE', 503);
define('HTTP_GATEWAY_TIMEOUT', 504);
define('HTTP_VERSION_NOT_SUPPORTED', 505);

define('ACTIVE', 1);
define('INACTIVE', 0);

define('INCORRECT_TOKEN_LIMIT', 3);

// type system
define('APP', 'app');
define('CMS_COMPANY', 'cms_company');
define('CMS_SYSTEM', 'cms_system');

// role
define('ADMIN_CMS_SYSTEM_ROLE', 'admin_cms_system');
define('ADMIN_CMS_COMPANY_ROLE', 'admin_cms_company');
define('MANAGER_ROLE', 'manager');
define('USER_ROLE', 'user');

// permission
define('LOGIN_APP', 'login_app');
define('LOGIN_CMS_COMPANY', 'login_cms_company');
define('LOGIN_CMS_SYSTEM', 'login_cms_system');
define('RESET_PASSWORD_CMS_COMPANY', 'reset_password_cms_company');
define('RESET_PASSWORD_APP', 'reset_password_app');

// type token password security
define('TOKEN_LOGIN_TYPE', 1);
define('TOKEN_RESET_PASS_TYPE', 2);
define('TOKEN_UPDATE_PASS_TYPE', 3);

// pageSize
define('PAGE_SIZE', 10);

// status property
define('WAIT_PROPERTY', 1);
define('APPROVED_PROPERTY', 2);
define('REJECT_PROPERTY', 3);

// file type
define('IMAGE_FILE_TYPE', 1);
define('DOCUMENT_FILE_TYPE', 2);

//  status customer
define('WAIT_CUSTOMER', 1);
define('APPROVED_CUSTOMER', 2);
define('REJECT_CUSTOMER', 3);

define('EMPTY_STREET', '以下に掲載がない場合');

//filter project
define('PROJECT_ME', 1);
define('PROJECT_ALL', 2);
define('PROJECT_DIVISION', 3);

define('MAX_UPLOAD_FILE_SIZE', 10240);
define('MAX_UPLOAD_FILE_IMPORT', 5120);


// calendar
define('PUBLIC_STATUS', 1);
define('IS_ACCEPT', 1);
define('UNKNOWN', 0);
define('REJECT', 2);
define('CALENDAR_ME', 1);
define('CALENDAR_ALL', 2);
define('CALENDAR_DIVISION', 3);
define('NOT_REPEAT', 1);
define('REPEAT_DAY', 2);
define('REPEAT_WEEK', 3);
define('REPEAT_MONTH', 4);
define('CALENDAR_ONE', 1);
define('IS_DELETED', 0);
define('IS_HOST', 1);
define('NO_NOTI', 1);
define('NO_FILE', 0);
// user type of project
define('USER_IN_CHARGE_TYPE', 1);
define('SUB_USER_IN_CHARGE_TYPE', 2);
define('RELATED_USER_TYPE', 3);

//phase project
define('NO_PHASE', 10);
define('PHASE_ONE', 1);
define('PHASE_TWO', 2);
define('PHASE_THREE', 3);
define('PHASE_FOUR', 4);
define('PHASE_FIVE', 5);
define('PHASE_SIX', 6);
define('PHASE_SEVEN', 7);
define('PHASE_EIGHT', 8);
define('PHASE_NINE', 9);
define('NO_PROJECT_CLOSE', 9);

define('IS_ACTION_NOTI', 1);
define('NO_ACTION_NOTI', 0);

define('PROJECT_TOTAL', 1);
define('PROJECT_IN_PROGRESS', 2);
define('PROJECT_CLOSE', 3);

// Project_close_status
define('IN_PROGRESS', 0);
define('REQUEST_CLOSE', 1);
define('FAIL_CLOSE', 2);
define('SUCCESS_CLOSE', 3);
define('REJECT_CLOSE', 4);
define('CALENDAR_PROJECT', 2);
define('PROJECT_CANCEL', 5);

define('EXIST_CERTIFICATE', 1);

// Project defalut commission rate
define('COMPANY_COMMISSION_RATE', 10);

// type ranking
define('CONTRACT_RANKING_TYPE', 1);
define('REVENUE_RANKING_TYPE', 2);
define('BROKERAGE_RAKING_TYPE', 3);

// ranking limit
define('RANKING_LIMIT', 1000);

//revenue Project
define('CONDITION_REVENUE_MIN', 2000000);
define('CONDITION_REVENUE_MAX', 4000000);

// Custom Field
define('PROPERTY', 1);
define('CUSTOMER', 2);
define('STRING_TYPE', 2);
define('TEXT_TYPE', 3);
define('STRING_LENGHT', 100);
define('TEXT_LENGHT', 300);

define('COMMISSION_RATE_MAX', 100);
define('NO_COMMISSION_RATE', 0);


define('POSITION', 1);

// ranking
define('SUBSCRIBE', 'subscribe');
define('UNSUBSCRIBE', 'unsubscibe');
define('RANKING_USER', 'ranking_user');
define('RANKING_DIVISION', 'ranking_division');
define('TOTAL_RANKING_DIVISION', 1);
define('TOTAL_RANKING_USER', 2);

// master data
define('MASTER_ADVERTISING_FORM', 1);
define('MASTER_ADVERTISING_WEB', 2);
define('MASTER_BROKERAGE_FEE', 3);
define('MASTER_CONTACT_METHOD', 4);
define('MASTER_CONTACT_REASON', 5);
define('MASTER_CONTACT_TYPE', 6);
define('MASTER_FIELD', 7);
define('MASTER_NOTIFY_CALENDAR', 8);
define('MASTER_PHASE_PROJECT', 9);
define('MASTER_POSITION', 10);
define('MASTER_POSTAL_CODE', 11);
define('MASTER_PRICE', 12);
define('MASTER_PROPERTY_BUILDING_STRUCTURE', 13);
define('MASTER_PROPERTY_CONTRACT_TYPE', 14);
define('MASTER_PROPERTY_CURRENT_SITUATION', 15);
define('MASTER_PROPERTY_TYPE', 16);
define('MASTER_PROVINCE', 17);
define('MASTER_PURCHASE_PURPOSE', 18);
define('MASTER_RAIL', 19);
define('MASTER_RESIDENCE_YEAR', 20);
define('MASTER_SALE_PURPOSE', 21);
define('MASTER_SCHEDULE_REPEAT', 22);
define('MASTER_STATION', 23);
define('DELETE_MASTER_DATA', 1);
define('UPDATE_MASTER_DATA', 2);


define('OTHER_ADVERTISING_FORM_ID', 12);
define('OTHER_PURCHASE_PURPOSE_ID', 9);

//color
define('COLOR_APP', 1);
define('COLOR_WEB', 2);

//type Calendar
define('MASTER_CALENDAR', 1);
define('SUB_CALENDAR', 2);

//noti calendar

define('NOTI_CALENDAR_5P', 3);
define('NOTI_CALENDAR_15P', 4);
define('NOTI_CALENDAR_30P', 5);
define('NOTI_CALENDAR_60p', 6);
define('NOTI_CALENDAR_1_DATE', 7);

//role
define('ADMIN_COMPANY', 2);
define('ROLE_ADMIN', 1);
