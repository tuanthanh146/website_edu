<?php
session_start();
require_once 'backend/config/database.php';

$error = '';

if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

if (isset($_GET['registered'])) {
    $success = 'Registration successful! Please login.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'All fields are required';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_picture'] = $user['picture'];

            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EDUAI Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .google-btn {
            background-color: #4285F4;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            margin-top: 10px;
        }
        .google-btn:hover {
            background-color: #357ABD;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h2 class="text-center mb-4">Login</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <div class="text-center mt-3">
                <p>Or login with</p>
                <a href="backend/api/auth/google.php" class="google-btn">
                    <img src="https://www.google.com/favicon.ico" alt="Google" style="width: 20px; margin-right: 10px;">
                    Login with Google
                </a>
            </div>
            
            <div class="text-center mt-3">
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </div>
    </div>
</body>
</html> 