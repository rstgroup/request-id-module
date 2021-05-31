<?php

$config = [
    // This should be an array of module namespaces used in the application.
    'modules' => [
        'RstGroup\RequestIdModule'
    ],

    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => [
        // This should be an array of paths in which modules reside.
        // If a string key is provided, the listener will consider that a module
        // namespace, the value of that key the specific path to that module's
        // Module class.
        'module_paths' => [
            __DIR__ . '/../../../..',
            __DIR__ . '/../../../../vendor',
        ],
        'config_cache_key' => 'application.config.cache',
        'config_cache_enabled' => false,
        'module_map_cache_key' => 'application.module.cache',
        'module_map_cache_enabled' => false,
        'cache_dir' => 'data/cache/',

        // An array of paths from which to glob configuration files after
        // modules are loaded. These effectively override configuration
        // provided by modules themselves. Paths may use GLOB_BRACE notation.
        'config_glob_paths' => [
            __DIR__ . "/autoload/{,*.}global.php",
        ],
    ],
];

if (class_exists(\Laminas\Router\Module::class)) {
    $config['modules'][] = \Laminas\Router\Module::class;
}

return $config;
