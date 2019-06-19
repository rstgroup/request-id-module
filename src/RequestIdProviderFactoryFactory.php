<?php
namespace RstGroup\RequestIdModule;

use PhpMiddleware\RequestId\Generator\GeneratorInterface;
use PhpMiddleware\RequestId\RequestIdProviderFactory;
use Psr\Container\ContainerInterface;

final class RequestIdProviderFactoryFactory
{
    public function __invoke(ContainerInterface $container): RequestIdProviderFactory
    {
        $generator = $container->get(GeneratorInterface::class);

        $config = $container->get('Config')['rst_group']['request_id_module'];

        return new RequestIdProviderFactory($generator, $config['allow_override'], $config['header_name']);
    }
}
