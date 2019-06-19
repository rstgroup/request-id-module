<?php
namespace RstGroup\RequestIdModule;

use PhpMiddleware\RequestId\Generator\GeneratorInterface;
use PhpMiddleware\RequestId\RequestIdProviderFactoryInterface;
use Psr\Container\ContainerInterface;

final class RequestIdListenerFactory
{
    public function __invoke(ContainerInterface $container): RequestIdListener
    {
        $requestIdGenerator = $container->get(GeneratorInterface::class);
        $requestIdProviderFactory = $container->get(RequestIdProviderFactoryInterface::class);

        $config = $container->get('Config')['rst_group']['request_id_module'];
        $requestIdHeaderName = $config['header_name'];

        return new RequestIdListener($requestIdProviderFactory, $requestIdHeaderName, $requestIdGenerator);
    }
}
