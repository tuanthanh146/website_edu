<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->name) &&
    !empty($data->email) &&
    !empty($data->password) &&
    !empty($data->confirmPassword)
) {
    if ($data->password !== $data->confirmPassword) {
        http_response_code(400);
        echo json_encode(["message" => "Mật khẩu không khớp"]);
        exit();
    }

    $user->name = $data->name;
    $user->email = $data->email;
    $user->password = $data->password;

    if ($user->create()) {
        http_response_code(201);
        echo json_encode([
            "message" => "Đăng ký thành công",
            "user" => [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email
            ]
        ]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Không thể đăng ký người dùng"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Không thể đăng ký. Dữ liệu không đầy đủ."]);
}
?> 