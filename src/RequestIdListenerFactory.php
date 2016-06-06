<?php
namespace RstGroup\RequestIdModule;

use PhpMiddleware\RequestId\Generator\GeneratorInterface;
use PhpMiddleware\RequestId\RequestIdProviderFactoryInterface;

class RequestIdListenerFactory
{
    public function __invoke($services)
    {
        $requestIdGenerator = $services->get(GeneratorInterface::class);
        $requestIdProviderFactory = $services->get(RequestIdProviderFactoryInterface::class);

        $config = $services->get('Config');
        $requestIdHeaderName = $config['rst_group']['request_id_module']['header_name'];

        return new RequestIdListener($requestIdProviderFactory, $requestIdHeaderName, $requestIdGenerator);
    }
}
