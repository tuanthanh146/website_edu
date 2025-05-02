<?php
require_once __DIR__ . '/../bootstrap.php';

// Nếu đã đăng nhập, chuyển hướng đến dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Lấy thông báo lỗi nếu có
$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']); // Xóa thông báo lỗi sau khi lấy
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/src/presentation/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h1>Welcome to <?php echo APP_NAME; ?></h1>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="username">Email:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="remember" value="1"> Ghi nhớ đăng nhập
                </label>
            </div>
            <button type="submit">Đăng nhập</button>
            <div class="form-links">
                <a href="forgot-password.php">Quên mật khẩu?</a>
                <span class="separator">|</span>
                <a href="register.php">Đăng ký tài khoản mới</a>
            </div>
        </form>
    </div>
</body>
</html> 