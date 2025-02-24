<?php

return [
    'central' => [
        'Central' => [
            [
                'label' => 'Dashboard',
                'icon' => 'home',
                'route' => 'dashboard'
            ],

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
        'Finance' => [

            [
                'label' => 'Plans',
                'icon' => 'users',
                'route' => 'plan.index'
            ],
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
