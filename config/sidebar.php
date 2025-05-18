<?php

return [
    "app" => [

        [
            'sl' => 1,
            'header' => 'Invoices',
        ],
         [
            'sl' => 2,
            'title' => 'Create',
            'icon' => 'bi bi-file-earmark-plus',
            'route' => 'invoices.create',
        ],
         [
            'sl' => 3,
            'title' => 'Invoice List',
            'icon' => 'fa-solid fa-list-ol',
            'route' => 'invoices.index',
        ],
        [
            'sl' => 4,
            'header' => 'Product',
        ],
         [
            'sl' => 5,
            'title' => 'Create',
            'icon' => 'bi bi-file-earmark-plus',
            'route' => 'products.create',
        ],
         [
            'sl' => 6,
            'title' => 'Product List',
            'icon' => 'fa-solid fa-list-ol',
            'route' => 'products.index',
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
