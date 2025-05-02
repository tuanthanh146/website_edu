<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'eduai');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application configuration
define('APP_NAME', 'EduAI');
define('APP_URL', 'http://localhost/WEBSITE_EDUAI');
define('APP_ROOT', dirname(__DIR__));

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1); 