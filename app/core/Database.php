<?php
// app/core/Database.php

class Database {
    private static ?mysqli $instance = null;

    public static function getInstance(): mysqli {
        if (self::$instance === null) {
            self::$instance = new mysqli(
                DB_HOST,
                DB_USER,
                DB_PASS,
                DB_NAME,
                DB_PORT
            );

            if (self::$instance->connect_error) {
                http_response_code(500);
                include __DIR__ . '/../views/errors/500.php';
                exit();
            }

            self::$instance->set_charset("utf8mb4");
        }

        return self::$instance;
    }

    private function __clone() {}
    public function __wakeup() {}
}