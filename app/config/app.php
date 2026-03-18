<?php
// app/config/app.php

// ===== URL =====
define('BASE_URL',  '/app');
define('BASE_PATH', __DIR__ . '/..');
define('BASE_VIEW_PATH', __DIR__ . '/views/..');
// app.php - sửa lại các constant
define('BASE_LOGIN_PATH',    '/app/index.php?url=login');
define('BASE_REGISTER_PATH', '/app/index.php?url=register');

define('APP_NAME',  'BonSai');

// ===== DATABASE =====
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bonsai2');