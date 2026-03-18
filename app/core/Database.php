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
                3307
            );

            if (self::$instance->connect_error) {
                die("Kết nối thất bại: " . self::$instance->connect_error);
            }

            self::$instance->set_charset("utf8mb4");
        }

        return self::$instance;
    }

    private function __clone() {}
    public function __wakeup() {}
}