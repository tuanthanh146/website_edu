<?php
session_start();
require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../../../config/social_auth.php';
require_once __DIR__ . '/../../../config/database.php';

use Firebase\JWT\JWT;

try {
    // Get the authorization code
    if (isset($_GET['code'])) {
        // Exchange code for access token
        $token = $google_client->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (!isset($token['error'])) {
            // Get user info
            $google_client->setAccessToken($token['access_token']);
            $google_service = new Google_Service_Oauth2($google_client);
            $user_info = $google_service->userinfo->get();
            
            // Check if user exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $user_info->email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if (!$user) {
                // Create new user
                $stmt = $conn->prepare("INSERT INTO users (full_name, email, google_id, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("sss", $user_info->name, $user_info->email, $user_info->id);
                
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
                // Update Google ID if not set
                if (empty($user['google_id'])) {
                    $stmt = $conn->prepare("UPDATE users SET google_id = ? WHERE id = ?");
                    $stmt->bind_param("si", $user_info->id, $user_id);
                    $stmt->execute();
                }
            }
            
            // Generate JWT token
            $payload = [
                'user_id' => $user_id,
                'email' => $user_info->email,
                'full_name' => $user_info->name,
                'iat' => time(),
                'exp' => time() + (60 * 60 * 24) // 24 hours
            ];
            
            $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
            
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $user_info->email;
            $_SESSION['full_name'] = $user_info->name;
            $_SESSION['jwt'] = $jwt;
            
            // Redirect to home with success
            header('Location: /WEBSITE_EDUAI/?login=success&token=' . urlencode($jwt));
            exit;
        }
    }
    
    throw new Exception("Invalid authorization code");
    
} catch (Exception $e) {
    error_log('Google Callback Error: ' . $e->getMessage());
    $_SESSION['error'] = 'Failed to complete Google login. Please try again.';
    header('Location: /WEBSITE_EDUAI/login');
    exit;
} 