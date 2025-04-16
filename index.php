<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Handle routing
$request = $_SERVER['REQUEST_URI'];
$base_path = '/WEBSITE_EDUAI';

// Remove base path from request
$request = str_replace($base_path, '', $request);

// If request is empty or just '/', load index
if (empty($request) || $request === '/') {
    require __DIR__ . '/frontend/index.php';
    exit;
}

// Handle API routes
if (strpos($request, '/api/') === 0) {
    $api_path = __DIR__ . '/backend' . $request . '.php';
    if (file_exists($api_path)) {
        require $api_path;
        exit;
    }
}

// Define routes
$routes = [
    '/login' => 'login.php',
    '/register' => 'register.php',
    '/logout' => 'logout.php',
    '/posts' => 'frontend/posts.php',
    '/post' => 'frontend/post.php',
    '/create-post' => 'frontend/create-post.php',
    '/dashboard' => 'frontend/dashboard.php',
    '/admin' => 'frontend/admin.php'
];

// Clean the request URL
$request = parse_url($request, PHP_URL_PATH);
$request = rtrim($request, '/');

// Check if route exists
if (isset($routes[$request])) {
    $file = __DIR__ . '/' . $routes[$request];
    if (file_exists($file)) {
        require $file;
        exit;
    }
}

// If no route found, show 404
http_response_code(404);
require __DIR__ . '/frontend/404.php'; 