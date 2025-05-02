<?php
require_once __DIR__ . '/../bootstrap.php';

use Business\AuthService;
use Data\UserRepository;

// Khởi tạo các service
$userRepository = new UserRepository();
$authService = new AuthService($userRepository);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    try {
        if (empty($username) || empty($password)) {
            throw new Exception('Vui lòng nhập email và mật khẩu');
        }

        if ($authService->login($username, $password)) {
            // Đăng nhập thành công
            header('Location: dashboard.php');
            exit;
        } else {
            throw new Exception('Email hoặc mật khẩu không chính xác');
        }
    } catch (Exception $e) {
        $_SESSION['login_error'] = $e->getMessage();
        header('Location: index.php');
        exit;
    }
} else {
    // Nếu không phải POST request, chuyển về trang đăng nhập
    header('Location: index.php');
    exit;
} 