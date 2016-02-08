<?php

return [
    'rst_group' => [
        'request_id_module' => [
            'header_name' => 'X-Request-Id',
            'allow_override'=> true,
        ]
    ],
    'service_manager' => [
        'factories' => [
            \PhpMiddleware\RequestId\RequestIdProviderFactoryInterface::class => \RstGroup\RequestIdModule\RequestIdProviderFactoryFactory::class,
            \RstGroup\RequestIdModule\RequestIdListener::class => \RstGroup\RequestIdModule\RequestIdListenerFactory::class,
        ],
        'invokables' => [
            \PhpMiddleware\RequestId\Generator\PhpUniqidGenerator::class => \PhpMiddleware\RequestId\Generator\PhpUniqidGenerator::class,
        ],
        'aliases' => [
            \PhpMiddleware\RequestId\RequestIdProviderInterface::class => \RstGroup\RequestIdModule\RequestIdListener::class,
            \PhpMiddleware\RequestId\Generator\GeneratorInterface::class => \PhpMiddleware\RequestId\Generator\PhpUniqidGenerator::class,
        ]
    ],
    'listeners' => [
        \RstGroup\RequestIdModule\RequestIdListener::class,
    ]
];