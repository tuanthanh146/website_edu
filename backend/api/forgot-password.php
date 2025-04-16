<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../config/mail.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email)) {
    $user->email = $data->email;

    if ($user->emailExists()) {
        $resetToken = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        if ($user->createPasswordReset($resetToken, $expiry)) {
            $resetLink = "http://localhost:5500/reset-password.html?token=" . $resetToken;
            
            $mail = new Mail();
            $mail->setTo($user->email);
            $mail->setSubject("Đặt lại mật khẩu - EduAI");
            $mail->setBody("
                <h1>Đặt lại mật khẩu</h1>
                <p>Xin chào {$user->name},</p>
                <p>Bạn đã yêu cầu đặt lại mật khẩu. Vui lòng nhấp vào liên kết bên dưới để đặt lại mật khẩu của bạn:</p>
                <p><a href='{$resetLink}'>{$resetLink}</a></p>
                <p>Liên kết này sẽ hết hạn sau 1 giờ.</p>
                <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
            ");

            if ($mail->send()) {
                http_response_code(200);
                echo json_encode(["message" => "Email đặt lại mật khẩu đã được gửi"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Không thể gửi email đặt lại mật khẩu"]);
            }
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Không thể tạo yêu cầu đặt lại mật khẩu"]);
        }
    } else {
        http_response_code(404);
        echo json_encode(["message" => "Email không tồn tại"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Email không được để trống"]);
}
?> 