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
            'users' => 'i,c,r,u,d',
            'tenant' => 'i,c,r,u,d',
            'company' => 'i,c,r,u,d',
            'roles' => 'i,c,r,u,d',
            'plan' => 'i,c,r,u,d',
            'profile' => 'i,r,u',
        ],
        RoleEnum::Busniss => [
            'profile' => 'r,u',
        ],
        RoleEnum::User => [
            'profile' => 'r,u',
        ],
    ],

    'permissions_map' => [
        'i' => 'index',
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],
];
