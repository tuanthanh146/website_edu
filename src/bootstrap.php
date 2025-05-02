<?php
// Load configuration
require_once __DIR__ . '/config/config.php';

// Load autoloader
require_once __DIR__ . '/config/autoload.php';

// Start session
session_start();

// Set default timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error handling
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}); 