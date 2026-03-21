<?php
// app/config/app.php

// ===== URL =====
define('BASE_URL',  '../app');
define('BASE_PATH', __DIR__ . '/..');
define('BASE_VIEW_PATH', __DIR__ . '/../views/');

// Paths dùng cho redirect — phải là URL web, không phải filesystem path
define('BASE_LOGIN_PATH',    '../app/index.php?url=login');
define('BASE_REGISTER_PATH', '../app/index.php?url=register');

define('APP_NAME', 'BonSai');
define('VIEWS_PATH', __DIR__ . '/../app/views');