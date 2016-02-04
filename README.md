# Request Id Module

This module provide integration possibility to generate and add request id to request/response's header.

## Installation

```json
"require": {
    "rstgroup/request-id-module": "dev-develop"
}
```


## Configuration

In your ZF2 application config add to module list

```php
return [
    'modules' => [
        'RstGroup\RequestIdModule',
    ],
];
```

also in your autoload config based on environment add generator configuration for `service_manager`, ex.

```php
return [
   'service_manager' => [
        'invokables' => [
            \PhpMiddleware\RequestId\Generator\GeneratorInterface::class => \PhpMiddleware\RequestId\Generator\PhpUniqidGenerator::class,
    ],
];
```

You can also change request header and not to allow ovveride request id by request header

```php
return [
    'rst_group' => [
        'request_id_module' => [
            'header' => 'X-Custoom-Request-Id',
            'allow_override'=> false,
        ]
    ],
]
```


