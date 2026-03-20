<?php
spl_autoload_register(function (string $class) {
    $paths = [
        __DIR__ . '/../models/'             . $class . '.php',
        __DIR__ . '/../controllers/'        . $class . '.php',
        __DIR__ . '/../controllers/admin/'  . $class . '.php', // ← thêm
        __DIR__ . '/../core/'               . $class . '.php',
        __DIR__ . '/../helpers/'            . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }

    // DEBUG TẠM — xóa sau khi fix xong
    error_log("Autoload FAIL: $class — không tìm thấy trong:\n" . implode("\n", $paths));
});