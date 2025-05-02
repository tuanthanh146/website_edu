<?php
require_once __DIR__ . '/../bootstrap.php';

use Business\AuthService;
use Data\UserRepository;

// Đảm bảo không có output nào trước khi set header
ob_start();

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['status' => 'ok']);
    exit();
}

// Khởi tạo các service
try {
    $userRepository = new UserRepository();
    $authService = new AuthService($userRepository);

    // Lấy phương thức HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    // Lấy path từ URL
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = str_replace('/WEBSITE_EDUAI/api', '', $path);

    // Xử lý các endpoint
    switch ($path) {
        case '/auth/login':
            if ($method === 'POST') {
                try {
                    // Lấy và kiểm tra input
                    $input = file_get_contents('php://input');
                    if (empty($input)) {
                        throw new Exception('No input data provided');
                    }

                    $data = json_decode($input, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('Invalid JSON format: ' . json_last_error_msg());
                    }

                    if (empty($data['username']) || empty($data['password'])) {
                        throw new Exception('Username and password are required');
                    }

                    // Thực hiện đăng nhập
                    if ($authService->login($data['username'], $data['password'])) {
                        echo json_encode([
                            'success' => true,
                            'message' => 'Login successful',
                            'user' => [
                                'id' => $_SESSION['user_id'],
                                'username' => $_SESSION['username']
                            ]
                        ]);
                    } else {
                        http_response_code(401);
                        echo json_encode([
                            'success' => false,
                            'error' => 'Invalid username or password'
                        ]);
                    }
                } catch (Exception $e) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                http_response_code(405);
                echo json_encode([
                    'success' => false,
                    'error' => 'Method not allowed. Only POST method is supported.'
                ]);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Endpoint not found'
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error: ' . $e->getMessage()
    ]);
}

// Đảm bảo tất cả output đã được gửi
ob_end_flush(); 