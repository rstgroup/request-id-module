<?php

namespace RstGroup\RequestIdModule;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

final class Module implements ConfigProviderInterface
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}