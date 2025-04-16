<?php
// Thông tin kết nối database
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Kết nối MySQL
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Đọc file SQL
    $sql = file_get_contents(__DIR__ . '/database/eduai.sql');

    // Thực thi các câu lệnh SQL
    $pdo->exec($sql);

    echo "Database đã được tạo thành công!\n";
    echo "Tài khoản admin: admin@eduai.com / 123456\n";
    echo "Tài khoản user: user@eduai.com / 123456\n";

} catch(PDOException $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}
?> 