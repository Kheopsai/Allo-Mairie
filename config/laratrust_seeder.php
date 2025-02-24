<?php

use App\Enums\RoleEnum;

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        RoleEnum::Admin => [
            'users' => 'c,r,u,d',
            'tenant' => 'c,r,u,d',
            'company'=>'c,r,u,d',
            'roles'=>'c,r,u,d',
            'plan'=>'c,r,u,d',
            'profile' => 'r,u',
        ],
        RoleEnum::User => [
            'profile' => 'r,u',
        ],
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],
];
