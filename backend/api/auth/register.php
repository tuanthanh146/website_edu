<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Include database configuration
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../config/database.php';

use Firebase\JWT\JWT;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    $data = $_POST;
}

// Validate required fields
$required_fields = ['full_name', 'email', 'password', 'confirm_password'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => ucfirst($field) . ' is required']);
        exit;
    }
}

// Validate email format
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

// Check if passwords match
if ($data['password'] !== $data['confirm_password']) {
    http_response_code(400);
    echo json_encode(['error' => 'Passwords do not match']);
    exit;
}

// Check password strength (at least 8 characters)
if (strlen($data['password']) < 8) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 8 characters long']);
    exit;
}

try {
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $data['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Email already registered']);
        exit;
    }

    // Hash password
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $data['full_name'], $data['email'], $hashed_password);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to create user");
    }

    $user_id = $stmt->insert_id;

    // Generate JWT token
    $payload = [
        'user_id' => $user_id,
        'email' => $data['email'],
        'full_name' => $data['full_name'],
        'iat' => time(),
        'exp' => time() + (60 * 60 * 24) // 24 hours
    ];

    $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

    // Set session variables
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $data['email'];
    $_SESSION['full_name'] = $data['full_name'];
    $_SESSION['jwt'] = $jwt;

    // Create user role if not exists
    $stmt = $conn->prepare("INSERT IGNORE INTO roles (name) VALUES ('user')");
    $stmt->execute();

    // Get user role ID
    $stmt = $conn->prepare("SELECT id FROM roles WHERE name = 'user'");
    $stmt->execute();
    $result = $stmt->get_result();
    $role = $result->fetch_assoc();
    $role_id = $role['id'];

    // Assign user role
    $stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $role_id);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'user' => [
            'id' => $user_id,
            'email' => $data['email'],
            'full_name' => $data['full_name']
        ],
        'token' => $jwt
    ]);

} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed. Please try again later.']);
    exit;
} 