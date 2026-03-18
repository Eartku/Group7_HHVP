<?php
spl_autoload_register(function (string $class) {
    $paths = [
        __DIR__ . '/../models/'      . $class . '.php',
        __DIR__ . '/../controllers/' . $class . '.php',
        __DIR__ . '/../core/'        . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});