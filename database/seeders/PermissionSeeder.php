<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->truncate();

        $permissions = [
            // login (id: 1-5)
            [
                'name' => 'Login CMS System',
                'slug' => 'login_cms_system',
            ],
            [
                'name' => 'Login CMS Company',
                'slug' => 'login_cms_company'
            ],
            [
                'name' => 'Login App',
                'slug' => 'login_app'
            ],
            [
                'name' => 'Reset password CMS Company',
                'slug' => 'reset_password_cms_company'
            ],
            [
                'name' => 'Reset password App',
                'slug' => 'reset_password_app'
            ],
            // division (id: 6-12)
            [
                'name' => 'Create Division',
                'slug' => 'create_division',
            ],
            [
                'name' => 'Show Division',
                'slug' => 'show_division',
            ],
            [
                'name' => 'Update Division',
                'slug' => 'update_division',
            ],
            [
                'name' => 'Delete Division',
                'slug' => 'delete_division',
            ],
            [
                'name' => 'Update Manager Division',
                'slug' => 'update_manager_division',
            ],
            [
                'name' => 'Update User Division',
                'slug' => 'update_user_division',
            ],
            [
                'name' => 'Delete User Division',
                'slug' => 'delete_user_division',
            ],
            //employee (id: 13-16)
            [
                'name' => 'Create Employee',
                'slug' => 'create_employee',
            ],
            [
                'name' => 'Show Employee',
                'slug' => 'show_employee',
            ],
            [
                'name' => 'Update Employee',
                'slug' => 'update_employee',
            ],
            [
                'name' => 'Delete Employee',
                'slug' => 'delete_employee',
            ],

            // property (id: 17-20)
            [
                'name' => 'Show Property',
                'slug' => 'show_property',
            ],
            [
                'name' => 'Create Property',
                'slug' => 'create_property',
            ],
            [
                'name' => 'Update Property',
                'slug' => 'update_property',
            ],
            [
                'name' => 'Delete Property',
                'slug' => 'delete_property',
            ],
            // customer (id: 21-24)
            [
                'name' => 'Create Customer',
                'slug' => 'create_customer',
            ],
            [
                'name' => 'Show Customer',
                'slug' => 'show_customer',
            ],
            [
                'name' => 'Update Customer',
                'slug' => 'update_customer',
            ],
            [
                'name' => 'Delete Customer',
                'slug' => 'delete_customer',
            ],

            // company (id: 25-28)
            [
                'name' => 'Create Company',
                'slug' => 'create_company',
            ],
            [
                'name' => 'Update Company',
                'slug' => 'update_company',
            ],
            [
                'name' => 'Delete Company',
                'slug' => 'delete_company',
            ],

            [
                'name' => 'Show Company',
                'slug' => 'show_company',
            ],
            // account CMS Company (id: 29-32)
            [
                'name' => 'Create Account CMS Company',
                'slug' => 'create_account_cms_company',
            ],
            [
                'name' => 'Upadte Account CMS Company',
                'slug' => 'update_account_cms_company',
            ],
            [
                'name' => 'Delete Account CMS Company',
                'slug' => 'delete_account_cms_company',
            ],

            [
                'name' => 'Show Account CMS Company',
                'slug' => 'show_account_cms_company',
            ],
            // project (id: 33-36)
            [
                'name' => 'Create Project',
                'slug' => 'create_project',
            ],
            [
                'name' => 'Upadte Project',
                'slug' => 'update_project',
            ],
            [
                'name' => 'Delete Project',
                'slug' => 'delete_project',
            ],
            [
                'name' => 'Show Project',
                'slug' => 'show_project',
            ],
            // report (id: 37-39)
            [
                'name' => 'Create Report',
                'slug' => 'create_report',
            ],
            [
                'name' => 'Update Report',
                'slug' => 'update_report',
            ],
            [
                'name' => 'Delete Report',
                'slug' => 'delete_report',
            ],

            // Calendar( id: 40-43)
            [
                'name' => 'Create Calendar',
                'slug' => 'create_calendar',
            ],
            [
                'name' => 'Show Calendar ',
                'slug' => 'show_calendar',
            ],
            [
                'name' => 'Update Calendar',
                'slug' => 'update_calendar',
            ],
            [
                'name' => 'Delete Calendar',
                'slug' => 'delete_calendar',
            ],

            // Import data( id: 44-47)
            [
                'name' => 'Import User',
                'slug' => 'import_user',
            ],
            [
                'name' => 'Import Division',
                'slug' => 'import_division',
            ],
            [
                'name' => 'Import Customer',
                'slug' => 'import_customer',
            ],
            [
                'name' => 'Import Property',
                'slug' => 'import_property',
            ],

            // custom field( id: 48-49)
            [
                'name' => 'Create Custom Field',
                'slug' => 'create_custom_field',
            ],
            [
                'name' => 'Show Custom Field',
                'slug' => 'show_custom_field',
            ],

            // contact( id: 50-51)
            [
                'name' => 'Show Contact',
                'slug' => 'show_contact',
            ],
            [
                'name' => 'Delete Contact',
                'slug' => 'delete_contact',
            ],

            // CMS Company( id: 52-53)
            [
                'name' => 'Show Company CMS Company',
                'slug' => 'show_cms_company',
            ],
            [
                'name' => 'Update Company CMS Company',
                'slug' => 'update_cms_company',
            ]
        ];

        DB::table('permissions')->insert($permissions);
    }
}
