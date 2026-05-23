<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Colombo');

define('APP_NAME', 'TechGear');
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('DATABASE_PATH', BASE_PATH . '/database');

define('DB_HOST', 'localhost');
define('DB_NAME', 'techgear');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('TAX_RATE', 0.08);
