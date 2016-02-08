<?php

return [
    'view_manager' => [
        'display_exceptions' => false,
        'not_found_template' => 'error/index',
        'exception_template' => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ],
    ],
];
