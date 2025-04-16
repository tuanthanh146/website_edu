<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log request information
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Request URI: " . $_SERVER['REQUEST_URI']);

// Define base path
define('BASE_PATH', realpath(__DIR__ . '/../../../'));

// Load Composer's autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Load database configuration
require_once BASE_PATH . '/backend/config/database.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

try {
    // Log environment variables for debugging
    error_log("Loading Google authentication...");
    error_log("GOOGLE_CLIENT_ID: " . $_ENV['GOOGLE_CLIENT_ID']);
    error_log("CALLBACK_URL: " . $_ENV['CALLBACK_URL']);

    // Configure Google Client
    $client = new Google\Client();
    $client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
    $client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
    $client->setRedirectUri($_ENV['CALLBACK_URL']);
    
    // Add required scopes
    $client->addScope('email');
    $client->addScope('profile');
    $client->addScope('openid');
    
    // Enable offline access
    $client->setAccessType('offline');
    $client->setPrompt('consent');
    $client->setIncludeGrantedScopes(true);

    // Handle Google authentication
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Generate authentication URL
        $authUrl = $client->createAuthUrl();
        
        // Log debugging information
        error_log("Generated Auth URL: " . $authUrl);
        
        // Redirect to Google's consent screen
        header('Location: ' . $authUrl);
        exit;
    }

    // Handle callback from Google
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        error_log("Handling POST request");
        error_log("POST data: " . print_r($_POST, true));
        
        $code = $_POST['code'] ?? '';
        
        if (empty($code)) {
            throw new Exception('Authorization code is required');
        }

        // Exchange authorization code for access token
        $token = $client->fetchAccessTokenWithAuthCode($code);
        if (isset($token['error'])) {
            throw new Exception('Error fetching access token: ' . $token['error']);
        }
        
        $client->setAccessToken($token);

        // Get user info
        $oauth2 = new Google\Service\Oauth2($client);
        $userInfo = $oauth2->userinfo->get();
        
        error_log("User Info: " . print_r($userInfo, true));
        
        // Check if user exists in database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$userInfo->email]);
        $user = $stmt->fetch();

        if (!$user) {
            // Create new user
            $stmt = $conn->prepare("
                INSERT INTO users (name, email, role, picture, created_at) 
                VALUES (?, ?, 'user', ?, NOW())
            ");
            $stmt->execute([
                $userInfo->name,
                $userInfo->email,
                $userInfo->picture
            ]);
            
            $userId = $conn->lastInsertId();
            error_log("Created new user with ID: " . $userId);
        } else {
            $userId = $user['id'];
            error_log("Found existing user with ID: " . $userId);
        }

        // Generate JWT token
        $jwt = generateJWT([
            'id' => $userId,
            'email' => $userInfo->email,
            'name' => $userInfo->name
        ]);

        // Start session and store user info
        session_start();
        $_SESSION['user_token'] = $jwt;
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $userInfo->name;
        $_SESSION['user_email'] = $userInfo->email;
        $_SESSION['user_picture'] = $userInfo->picture;

        error_log("Session data set: " . print_r($_SESSION, true));

        // Redirect to home page
        header('Location: /WEBSITE_EDUAI/');
        exit;
    }

} catch (Exception $e) {
    error_log("Google Auth Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    header('Location: /WEBSITE_EDUAI/login.php?error=' . urlencode($e->getMessage()));
    exit;
}

// Function to generate JWT token
function generateJWT($payload) {
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode($payload);
    
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    
    $signature = hash_hmac('sha256', 
        $base64UrlHeader . "." . $base64UrlPayload, 
        $_ENV['JWT_SECRET'], 
        true
    );
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
} 