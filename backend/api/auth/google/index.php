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
    // Configure Google Client
    $client = new Google_Client();
    $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
    $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
    $client->setRedirectUri($_ENV['APP_URL'] . '/api/auth/google/callback');
    $client->addScope('email');
    $client->addScope('profile');

    // Get authorization URL
    $auth_url = $client->createAuthUrl();
    
    // Log for debugging
    error_log("Google Auth URL: " . $auth_url);
    
    // Redirect to Google
    header('Location: ' . $auth_url);
    exit;
    
} catch (Exception $e) {
    error_log('Google Auth Error: ' . $e->getMessage());
    $_SESSION['error'] = 'Failed to initialize Google login. Please try again.';
    header('Location: /WEBSITE_EDUAI/login');
    exit;
} 