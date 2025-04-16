<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /WEBSITE_EDUAI/');
    exit;
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EduAI Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/WEBSITE_EDUAI/assets/css/style.css" rel="stylesheet">
    <style>
        .social-login-button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
        }
        .google-login {
            background-color: #fff;
        }
        .google-login:hover {
            background-color: #f1f1f1;
        }
        .facebook-login {
            background-color: #1877f2;
            color: white;
        }
        .facebook-login:hover {
            background-color: #166fe5;
            color: white;
        }
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #ddd;
        }
        .divider span {
            padding: 0 10px;
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="bg-light">
    <?php include_once __DIR__ . '/../components/navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Login</h2>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <div id="errorAlert" class="alert alert-danger d-none"></div>

                        <!-- Social Login Buttons -->
                        <a href="/WEBSITE_EDUAI/backend/api/auth/google" class="social-login-button google-login mb-2">
                            <i class="fab fa-google"></i>
                            <span>Continue with Google</span>
                        </a>
                        
                        <a href="/WEBSITE_EDUAI/backend/api/auth/facebook" class="social-login-button facebook-login mb-3">
                            <i class="fab fa-facebook-f"></i>
                            <span>Continue with Facebook</span>
                        </a>

                        <div class="divider">
                            <span>OR</span>
                        </div>

                        <form id="loginForm" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="invalid-feedback">Please enter your password.</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                            
                            <div class="text-center">
                                <p class="mb-0">Don't have an account? <a href="/WEBSITE_EDUAI/register">Register here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const form = e.target;
            const errorAlert = document.getElementById('errorAlert');
            
            // Reset alert
            errorAlert.classList.add('d-none');
            
            // Basic form validation
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            // Prepare form data
            const formData = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };

            try {
                const response = await fetch('/WEBSITE_EDUAI/api/auth/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Login failed');
                }

                // Store the JWT token
                localStorage.setItem('jwt_token', data.token);
                
                // Redirect to homepage
                window.location.href = '/WEBSITE_EDUAI/';

            } catch (error) {
                errorAlert.textContent = error.message;
                errorAlert.classList.remove('d-none');
            }
        });
    </script>
</body>
</html> 