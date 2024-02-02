<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('role_permissions')->truncate();

        $permissions = [
            // login (id: 1-5)
            [
                'role_id' => 1,
                'permission_id' => 1,
            ],
            [
                'role_id' => 2,
                'permission_id' => 2,
            ],
            [
                'role_id' => 2,
                'permission_id' => 3,
            ],
            [
                'role_id' => 3,
                'permission_id' => 2,
            ],
            [
                'role_id' => 3,
                'permission_id' => 3,
            ],
            [
                'role_id' => 4,
                'permission_id' => 2,
            ],
            [
                'role_id' => 4,
                'permission_id' => 3,
            ],

            // reset password
            [
                'role_id' => 2,
                'permission_id' => 4,
            ],
            [
                'role_id' => 2,
                'permission_id' => 5,
            ],
            [
                'role_id' => 3,
                'permission_id' => 4,
            ],
            [
                'role_id' => 4,
                'permission_id' => 4,
            ],
            [
                'role_id' => 3,
                'permission_id' => 5,
            ],
            [
                'role_id' => 4,
                'permission_id' => 5,
            ],

            // divisions (id: 6-12)
            [
                'role_id' => 2,
                'permission_id' => 6,
            ],
            [
                'role_id' => 2,
                'permission_id' => 7,
            ],
            [
                'role_id' => 3,
                'permission_id' => 7,
            ],
            [
                'role_id' => 4,
                'permission_id' => 7,
            ],
            [
                'role_id' => 2,
                'permission_id' => 8,
            ],
            [
                'role_id' => 2,
                'permission_id' => 9,
            ],
            [
                'role_id' => 2,
                'permission_id' => 10,
            ],
            [
                'role_id' => 2,
                'permission_id' => 11,
            ],
            [
                'role_id' => 2,
                'permission_id' => 12,
            ],
            [
                'role_id' => 3,
                'permission_id' => 12,
            ],

            // employee
            [
                'role_id' => 2,
                'permission_id' => 13,
            ],
            [
                'role_id' => 3,
                'permission_id' => 13,
            ],
            [
                'role_id' => 4,
                'permission_id' => 13,
            ],
            [
                'role_id' => 1,
                'permission_id' => 14,
            ],
            [
                'role_id' => 2,
                'permission_id' => 14,
            ],
            [
                'role_id' => 3,
                'permission_id' => 14,
            ],
            [
                'role_id' => 4,
                'permission_id' => 14,
            ],
            [
                'role_id' => 1,
                'permission_id' => 15,
            ],
            [
                'role_id' => 2,
                'permission_id' => 15,
            ],
            [
                'role_id' => 3,
                'permission_id' => 15,
            ],
            [
                'role_id' => 4,
                'permission_id' => 15,
            ],

            [
                'role_id' => 2,
                'permission_id' => 16,
            ],
            [
                'role_id' => 3,
                'permission_id' => 16,
            ],
            [
                'role_id' => 4,
                'permission_id' => 16,
            ],
            // property
            [
                'role_id' => 2,
                'permission_id' => 17,
            ],
            [
                'role_id' => 3,
                'permission_id' => 17,
            ],
            [
                'role_id' => 4,
                'permission_id' => 17,
            ],
            [
                'role_id' => 2,
                'permission_id' => 18,
            ],
            [
                'role_id' => 3,
                'permission_id' => 18,
            ],
            [
                'role_id' => 4,
                'permission_id' => 18
            ],
            [
                'role_id' => 2,
                'permission_id' => 19,
            ],
            [
                'role_id' => 3,
                'permission_id' => 19,
            ],
            [
                'role_id' => 4,
                'permission_id' => 19,
            ],
            [
                'role_id' => 2,
                'permission_id' => 20,
            ],
            [
                'role_id' => 3,
                'permission_id' => 20,
            ],

            // Customer
            [
                'role_id' => 2,
                'permission_id' => 21,
            ],
            [
                'role_id' => 3,
                'permission_id' => 21,
            ],
            [
                'role_id' => 4,
                'permission_id' => 21,
            ],
            [
                'role_id' => 2,
                'permission_id' => 22,
            ],
            [
                'role_id' => 3,
                'permission_id' => 22,
            ],
            [
                'role_id' => 4,
                'permission_id' => 22,
            ],
            [
                'role_id' => 2,
                'permission_id' => 23,
            ],
            [
                'role_id' => 3,
                'permission_id' => 23,
            ],
            [
                'role_id' => 4,
                'permission_id' => 23,
            ],
            [
                'role_id' => 2,
                'permission_id' => 24,
            ],
            [
                'role_id' => 3,
                'permission_id' => 24,
            ],

            //company
            [
                'role_id' => 1,
                'permission_id' => 25,
            ],
            [
                'role_id' => 1,
                'permission_id' => 26,
            ],
            [
                'role_id' => 2,
                'permission_id' => 26,
            ],
            [
                'role_id' => 2,
                'permission_id' => 26,
            ],
            [
                'role_id' => 1,
                'permission_id' => 27,
            ],
            [
                'role_id' => 1,
                'permission_id' => 28,
            ],
            [
                'role_id' => 2,
                'permission_id' => 28,
            ],
            // account cms company
            [
                'role_id' => 1,
                'permission_id' => 29,
            ],
            [
                'role_id' => 1,
                'permission_id' => 30,
            ],
            [
                'role_id' => 1,
                'permission_id' => 31,
            ],
            [
                'role_id' => 1,
                'permission_id' => 32,
            ],
            // project
            [
                'role_id' => 2,
                'permission_id' => 33,
            ],
            [
                'role_id' => 3,
                'permission_id' => 33,
            ],
            [
                'role_id' => 4,
                'permission_id' => 33,
            ],
            [
                'role_id' => 2,
                'permission_id' => 34,
            ],
            [
                'role_id' => 3,
                'permission_id' => 34,
            ],
            [
                'role_id' => 4,
                'permission_id' => 34,
            ],
            [
                'role_id' => 2,
                'permission_id' => 35,
            ],
            [
                'role_id' => 3,
                'permission_id' => 35,
            ],
            [
                'role_id' => 4,
                'permission_id' => 35,
            ],
            [
                'role_id' => 2,
                'permission_id' => 36,
            ],
            [
                'role_id' => 3,
                'permission_id' => 36,
            ],
            [
                'role_id' => 4,
                'permission_id' => 36,
            ],
            // report 37-39
            [
                'role_id' => 2,
                'permission_id' => 37,
            ],
            [
                'role_id' => 3,
                'permission_id' => 37,
            ],
            [
                'role_id' => 4,
                'permission_id' => 37,
            ],
            [
                'role_id' => 2,
                'permission_id' => 38,
            ],
            [
                'role_id' => 3,
                'permission_id' => 38,
            ],
            [
                'role_id' => 4,
                'permission_id' => 38,
            ],
            [
                'role_id' => 2,
                'permission_id' => 39,
            ],
            [
                'role_id' => 3,
                'permission_id' => 39,
            ],

            [
                'role_id' => 4,
                'permission_id' => 39,
            ],

            // Calendar (id:40-44)
            [
                'role_id' => 2,
                'permission_id' => 40,
            ],
            [
                'role_id' => 3,
                'permission_id' => 40,
            ],
            [
                'role_id' => 4,
                'permission_id' => 40,
            ],
            [
                'role_id' => 2,
                'permission_id' => 41,
            ],
            [
                'role_id' => 3,
                'permission_id' => 41,
            ],
            [
                'role_id' => 4,
                'permission_id' => 41,
            ],
            [
                'role_id' => 2,
                'permission_id' => 42,
            ],
            [
                'role_id' => 3,
                'permission_id' => 42,
            ],
            [
                'role_id' => 4,
                'permission_id' => 42,
            ],
            [
                'role_id' => 2,
                'permission_id' => 43,
            ],
            [
                'role_id' => 3,
                'permission_id' => 43,
            ],
            [
                'role_id' => 4,
                'permission_id' => 43,
            ],

            // Import data (id:44-47)
            [
                'role_id' => 2,
                'permission_id' => 44,
            ],
            [
                'role_id' => 3,
                'permission_id' => 44,
            ],

            [
                'role_id' => 2,
                'permission_id' => 45,
            ],
            [
                'role_id' => 2,
                'permission_id' => 46,
            ],
            [
                'role_id' => 3,
                'permission_id' => 46,
            ],
            [
                'role_id' => 4,
                'permission_id' => 46,
            ],
            [
                'role_id' => 2,
                'permission_id' => 47,
            ],
            [
                'role_id' => 3,
                'permission_id' => 47,
            ],
            [
                'role_id' => 4,
                'permission_id' => 47,
            ],

            // custom field
            [
                'role_id' => 1,
                'permission_id' => 48,
            ],
            [
                'role_id' => 2,
                'permission_id' => 48,
            ],
            [
                'role_id' => 2,
                'permission_id' => 49,
            ],
            [
                'role_id' => 3,
                'permission_id' => 49,
            ],
            [
                'role_id' => 4,
                'permission_id' => 49,
            ],

            // contact
            [
                'role_id' => 1,
                'permission_id' => 50
            ],
            [
                'role_id' => 1,
                'permission_id' => 51
            ],

            // cms company
            [
                'role_id' => 1,
                'permission_id' => 52
            ],
            [
                'role_id' => 2,
                'permission_id' => 52
            ],
            [
                'role_id' => 3,
                'permission_id' => 52
            ],
            [
                'role_id' => 4,
                'permission_id' => 52
            ],
            [
                'role_id' => 2,
                'permission_id' => 53
            ]
        ];

        DB::table('role_permissions')->insert($permissions);
    }
}
