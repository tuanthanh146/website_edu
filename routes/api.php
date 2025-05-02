<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';

use Bramus\Router\Router;

$router = new Router();

// Parse request body
$request = json_decode(file_get_contents('php://input'), true) ?? [];

// AI Routes
$router->post('/ai/ask', function() use ($db, $request) {
    $controller = new App\Controllers\AIController($db);
    echo $controller->askQuestion($request);
});

$router->post('/ai/train', function() use ($db, $request) {
    $controller = new App\Controllers\AIController($db);
    echo $controller->trainModel($request);
});

$router->get('/ai/training-data', function() use ($db, $request) {
    $controller = new App\Controllers\AIController($db);
    echo $controller->getTrainingData($request);
});

// AI Tutor Routes
$router->post('/ai/tutor/start', function() use ($db, $request) {
    $controller = new App\Controllers\AITutorController($db);
    echo $controller->startSession($request);
});

$router->post('/ai/tutor/progress', function() use ($db, $request) {
    $controller = new App\Controllers\AITutorController($db);
    echo $controller->updateProgress($request);
});

$router->post('/ai/tutor/end', function() use ($db, $request) {
    $controller = new App\Controllers\AITutorController($db);
    echo $controller->endSession($request);
});

// AI 3D Routes
$router->post('/ai3d/start', function() use ($db, $request) {
    $controller = new App\Controllers\AI3DController($db);
    echo $controller->startSession($request);
});

$router->post('/ai3d/interact', function() use ($db, $request) {
    $controller = new App\Controllers\AI3DController($db);
    echo $controller->processInteraction($request);
});

$router->post('/ai3d/end', function() use ($db, $request) {
    $controller = new App\Controllers\AI3DController($db);
    echo $controller->endSession($request);
});

$router->run(); 