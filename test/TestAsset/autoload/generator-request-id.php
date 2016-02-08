<?php

return [
    'service_manager' => [
        'invokables' => [
            \RstGroup\RequestIdModule\Test\TestAsset\FakeGenerator::class => \RstGroup\RequestIdModule\Test\TestAsset\FakeGenerator::class,
        ],
        'aliases' => [
            \PhpMiddleware\RequestId\Generator\GeneratorInterface::class => \RstGroup\RequestIdModule\Test\TestAsset\FakeGenerator::class,
        ],
    ],
];