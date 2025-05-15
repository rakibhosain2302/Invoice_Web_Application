<?php

return [
    "app" => [

        [
            'sl' => 1,
            'header' => 'Authontication',
            'permission' => 'auth',
        ],
        [
            'sl' => 2,
            'title' => "User",
            'icon' => 'fas fa-user',
            'url' => '/authorization/users'
        ],
        [
            'sl' => 3,
            'title' => "Role",
            'icon' => 'fas fa-user-cog',
            'url' => '/authorization/roles'
        ],
       
        [
            'sl' => 4,
            'header' => 'Invoices',
        ],
         [
            'sl' => 5,
            'title' => 'Create',
            'icon' => 'bi bi-file-earmark-plus',
            'route' => 'invoices.create',
        ],
         [
            'sl' => 6,
            'title' => 'Invoice List',
            'icon' => 'fa-solid fa-list-ol',
            'route' => 'invoices.index',
        ],
        [
            'sl' => 7,
            'header' => 'Setttings'
        ],
        [
            'sl' => 8,
            'title' => "General Setting",
            'icon' => 'bi bi-gear',
            'url' => '/settings'
        ],

    ]
];
