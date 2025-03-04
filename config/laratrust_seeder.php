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
            'user' => 'i,c,r,u,d',
            'tenant' => 'i,c,r,u,d',
            'company' => 'i,c,r,u,d',
            'permission' => 'i,c',
            'role' => 'i,c,r,u,d',
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

    'tenant_roles_structure' => [
        RoleEnum::Admin => [
            'user' => 'i,c,r,u,d',
            'company' => 'r,u,d',
            'hub' => 'i,c,r,u,d',
            'chat' => 'i,c,r,u,d',
            'source' => 'i,c,r,u,d',
            'permission' => 'i,c',
            'role' => 'i,c,r,u,d',
            'profile' => 'i,r,u',
            'creditRequest'=> 'i,c,r,u,d',
        ],
        RoleEnum::User => [
            'hub' => 'i,c,r',
            'source' => 'i,c,r',
            'chat' => 'i,c,r',
            'profile' => 'r,u',
            'creditRequest'=> 'c,r',
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
