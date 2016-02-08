<?php
namespace RstGroup\RequestIdModule;

use PhpMiddleware\RequestId\RequestIdProviderFactoryInterface;

class RequestIdListenerFactory
{
    public function __invoke($services)
    {
        $requestIdProviderFactory = $services->get(RequestIdProviderFactoryInterface::class);

        $config = $services->get('Config');
        $requestIdHeaderName = $config['rst_group']['request_id_module']['header_name'];

        return new RequestIdListener($requestIdProviderFactory, $requestIdHeaderName);
    }
}