<?php

spl_autoload_register(function ($class) {

    // защита от path traversal
    $class = preg_replace('/[^a-zA-Z0-9_]/', '', $class);

    $paths = [
        __DIR__ . '/../app/Controllers/',
        __DIR__ . '/../app/Models/',
        __DIR__ . '/../app/Middleware/',
        __DIR__ . '/',
    ];

    foreach ($paths as $path) {

        $file = $path . $class . '.php';

        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});