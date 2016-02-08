<?php
namespace RstGroup\RequestIdModule;

use PhpMiddleware\RequestId\Generator\GeneratorInterface;
use PhpMiddleware\RequestId\RequestIdProviderFactory;

class RequestIdProviderFactoryFactory
{
    public function __invoke($services)
    {
        $generator = $services->get(GeneratorInterface::class);

        $config = $services->get('Config');
        $allowOverride = $config['rst_group']['request_id_module']['allow_override'];
        $requestIdHeaderName = $config['rst_group']['request_id_module']['header_name'];

        return new RequestIdProviderFactory($generator, $allowOverride, $requestIdHeaderName);
    }
}