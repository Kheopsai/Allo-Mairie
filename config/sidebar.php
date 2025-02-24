<?php

return [

    'central' => [

        [
            'label' => 'Dashboard',
            'icon' => 'home',
            'route' => 'dashboard'
        ],
        'Central' => [

            [
                'label' => 'Companies',
                'icon' => 'building-office-2',
                'route' => 'metropole.index'
            ],
            [
                'label' => 'Users',
                'icon' => 'users',
                'route' => 'user.index'
            ],


        ],

        'Machines' => [

            [
                'label' => 'Tenants',
                'icon' => 'globe-alt',
                'route' => 'tenant.index'
            ],
        ],
        'Finance' => [

            [
                'label' => 'Plans',
                'icon' => 'users',
                'route' => 'plan.index'
            ],
        ],
        'Roles And Permissions'=>[
            [
                'label'=> 'Roles',
                'icon' => 'shield-check',
                'route'=>'role.index'
            ]
        ],

        'Settings' => [

            [
                'label' => 'Settings',
                'icon' => 'cog-6-tooth',
                'route' => 'setting.index'
            ],
        ]
    ],

    'tenant' => [
        [
            'label' => 'Dashboard',
            'icon' => 'home',
            'route' => 'dashboard'
        ],
        [
            'label' => 'Chat',
            'icon' => 'chat-bubble-oval-left',
            'route' => 'chat.index'
        ],
        'Data Base' => [
            [
                'label' => 'DataBase',
                'icon' => 'circle-stack',
                'route' => 'hub.index'
            ]
        ]
    ]
];
