<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../../../config/database.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../../');
$dotenv->load();

try {
    $fb = new Facebook\Facebook([
        'app_id' => $_ENV['FACEBOOK_APP_ID'],
        'app_secret' => $_ENV['FACEBOOK_APP_SECRET'],
        'default_graph_version' => 'v12.0'
    ]);

    $helper = $fb->getRedirectLoginHelper();
    
    // Optional permissions
    $permissions = ['email'];
    $callback_url = $_ENV['APP_URL'] . '/api/auth/facebook/callback';
    
    // Get login URL
    $auth_url = $helper->getLoginUrl($callback_url, $permissions);
    
    // Log for debugging
    error_log("Facebook Auth URL: " . $auth_url);
    
    // Redirect to Facebook
    header('Location: ' . $auth_url);
    exit;
    
} catch(Exception $e) {
    error_log('Facebook Auth Error: ' . $e->getMessage());
    $_SESSION['error'] = 'Failed to initialize Facebook login. Please try again.';
    header('Location: /WEBSITE_EDUAI/login');
    exit;
} 