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
    <title>Register - EduAI Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/WEBSITE_EDUAI/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include_once __DIR__ . '/../components/navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Create an Account</h2>
                        
                        <div id="errorAlert" class="alert alert-danger d-none"></div>
                        <div id="successAlert" class="alert alert-success d-none"></div>

                        <form id="registerForm" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                                <div class="invalid-feedback">Please enter your full name.</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="8">
                                <div class="invalid-feedback">Password must be at least 8 characters long.</div>
                            </div>

                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <div class="invalid-feedback">Passwords must match.</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">Register</button>
                            
                            <div class="text-center">
                                <p class="mb-0">Already have an account? <a href="/WEBSITE_EDUAI/login">Login here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const form = e.target;
            const errorAlert = document.getElementById('errorAlert');
            const successAlert = document.getElementById('successAlert');
            
            // Reset alerts
            errorAlert.classList.add('d-none');
            successAlert.classList.add('d-none');
            
            // Basic form validation
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            // Check if passwords match
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                errorAlert.textContent = 'Passwords do not match';
                errorAlert.classList.remove('d-none');
                return;
            }

            // Prepare form data
            const formData = {
                full_name: document.getElementById('full_name').value,
                email: document.getElementById('email').value,
                password: password,
                confirm_password: confirmPassword
            };

            try {
                const response = await fetch('/WEBSITE_EDUAI/api/auth/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Registration failed');
                }

                // Store the JWT token
                localStorage.setItem('jwt_token', data.token);
                
                // Show success message
                successAlert.textContent = 'Registration successful! Redirecting to homepage...';
                successAlert.classList.remove('d-none');
                
                // Clear form
                form.reset();
                form.classList.remove('was-validated');

                // Redirect to homepage after a short delay
                setTimeout(() => {
                    window.location.href = '/WEBSITE_EDUAI/';
                }, 2000);

            } catch (error) {
                errorAlert.textContent = error.message;
                errorAlert.classList.remove('d-none');
            }
        });
    </script>
</body>
</html> 