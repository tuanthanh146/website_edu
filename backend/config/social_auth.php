<?php
require_once __DIR__ . '/../../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Google configuration
$google_config = [
    'client_id' => $_ENV['GOOGLE_CLIENT_ID'],
    'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'],
    'redirect_uri' => $_ENV['APP_URL'] . '/api/auth/google/callback'
];

// Facebook configuration
$facebook_config = [
    'app_id' => $_ENV['FACEBOOK_APP_ID'],
    'app_secret' => $_ENV['FACEBOOK_APP_SECRET'],
    'redirect_uri' => $_ENV['APP_URL'] . '/api/auth/facebook/callback',
    'default_graph_version' => 'v12.0'
];

// Initialize Google Client
$google_client = new Google_Client();
$google_client->setClientId($google_config['client_id']);
$google_client->setClientSecret($google_config['client_secret']);
$google_client->setRedirectUri($google_config['redirect_uri']);
$google_client->addScope('email');
$google_client->addScope('profile');

// Initialize Facebook SDK
$fb = new Facebook\Facebook([
    'app_id' => $facebook_config['app_id'],
    'app_secret' => $facebook_config['app_secret'],
    'default_graph_version' => $facebook_config['default_graph_version']
]); 