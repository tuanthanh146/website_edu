<?php
session_start();
require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../../../config/social_auth.php';
require_once __DIR__ . '/../../../config/database.php';

use Firebase\JWT\JWT;

try {
    $helper = $fb->getRedirectLoginHelper();
    
    // Get access token
    $access_token = $helper->getAccessToken();
    
    if (!$access_token) {
        throw new Exception('Failed to get access token');
    }
    
    // Set access token
    $fb->setDefaultAccessToken($access_token);
    
    // Get user data
    $response = $fb->get('/me?fields=id,name,email');
    $user_info = $response->getGraphUser();
    
    if (!$user_info->getEmail()) {
        throw new Exception('Email is required');
    }
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $user_info->getEmail());
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        // Create new user
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, facebook_id, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $user_info->getName(), $user_info->getEmail(), $user_info->getId());
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to create user");
        }
        
        $user_id = $stmt->insert_id;
        
        // Create user role if not exists and assign it
        $stmt = $conn->prepare("INSERT IGNORE INTO roles (name) VALUES ('user')");
        $stmt->execute();
        
        $stmt = $conn->prepare("SELECT id FROM roles WHERE name = 'user'");
        $stmt->execute();
        $role = $stmt->get_result()->fetch_assoc();
        
        $stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $role['id']);
        $stmt->execute();
    } else {
        $user_id = $user['id'];
        // Update Facebook ID if not set
        if (empty($user['facebook_id'])) {
            $stmt = $conn->prepare("UPDATE users SET facebook_id = ? WHERE id = ?");
            $stmt->bind_param("si", $user_info->getId(), $user_id);
            $stmt->execute();
        }
    }
    
    // Generate JWT token
    $payload = [
        'user_id' => $user_id,
        'email' => $user_info->getEmail(),
        'full_name' => $user_info->getName(),
        'iat' => time(),
        'exp' => time() + (60 * 60 * 24) // 24 hours
    ];
    
    $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    
    // Set session variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $user_info->getEmail();
    $_SESSION['full_name'] = $user_info->getName();
    $_SESSION['jwt'] = $jwt;
    
    // Redirect to home with success
    header('Location: /WEBSITE_EDUAI/?login=success&token=' . urlencode($jwt));
    exit;
    
} catch(Exception $e) {
    error_log('Facebook Callback Error: ' . $e->getMessage());
    $_SESSION['error'] = 'Failed to complete Facebook login. Please try again.';
    header('Location: /WEBSITE_EDUAI/login');
    exit;
} 