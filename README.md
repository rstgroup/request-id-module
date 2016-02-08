# Request Id Module

[![Build Status](https://travis-ci.org/rstgroup/request-id-module.svg?branch=master)](https://travis-ci.org/rstgroup/request-id-module)

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

You can also change request header and not to allow ovveride request id by request header

```php
return [
    'rst_group' => [
        'request_id_module' => [
            'header' => 'X-Custom-Request-Id',
            'allow_override'=> false,
        ],
    ],
];
```

### Generator

In your autoload config based on environment you can change default `PhpUniqidGenerator` to other, for example you can use md5 generator:

```php
return [
   'service_manager' => [
       'invokables' => [
           \PhpMiddleware\RequestId\Generator\GeneratorInterface::class => \PhpMiddleware\RequestId\Generator\Md5Generator::class,
       ],
    ],
];
```


