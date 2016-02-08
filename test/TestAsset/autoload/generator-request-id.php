<?php

return [
    'service_manager' => [
        'invokables' => [
            \PhpMiddleware\RequestId\Generator\GeneratorInterface::class => \RstGroup\RequestIdModule\Test\TestAsset\FakeGenerator::class,
        ],
    ],
];