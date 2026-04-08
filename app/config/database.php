<?php
// app/config/database.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bonsai2');
define('DB_PORT', 3307);
// luồng hoạt dộng trên router
// Client(View) --> Controller --> Model --> Database
// --> Model --> Controller --> Client(View)