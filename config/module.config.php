<?php

return [
    'rst_group' => [
        'request_id_module' => [
            'header' => 'X-Request-Id',
            'allow_override'=> true,
        ]
    ],
    'service_manager' => [
        'factories' => [
            \PhpMiddleware\RequestId\RequestIdProviderFactoryInterface::class => \RstGroup\RequestIdModule\RequestIdProviderFactoryFactory::class,
            \RstGroup\RequestIdModule\RequestIdListener::class => \RstGroup\RequestIdModule\RequestIdListenerFactory::class,
        ]
    ],
    'listeners' => [
        \RstGroup\RequestIdModule\RequestIdListener::class,
    ]
];