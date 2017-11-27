<?php

spl_autoload_register(function ($className) {
    $classes = [
        'API' => '/api/',
        'APIFactory' => '/api/',
        'Router' => '/api/',
        'Home' => '/api/',

        'Render' => '/views/',
        'Template' => '/views/',

        'Logger' => '/system/'
    ];

    if ($className === 'Config') {
        require_once __DIR__ . '/Config.php';
    } elseif (file_exists(__DIR__ . $classes[$className] . $className . '.php')) {
        require_once __DIR__ . $classes[$className] . $className . '.php';
    }
});

new Config;

