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
                'label' => 'Metropoles',
                'icon' => 'building-office-2',
                'route' => 'metropole.index'
            ],
            [
                'label' => 'Users',
                'icon' => 'users',
                'route' => 'user.index'
            ],

        ],
    ],
    'tenant'=> [
        [
            'label'=>'Dashboard',
            'icon'=> 'home',
            'route'=>'dashboard'
        ]
        ,
        [
            'label'=>'Chat',
            'icon'=>'chat-bubble-oval-left',
            'route'=>'chat.index'
        ]
    ]
];
