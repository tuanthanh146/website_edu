document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const rememberCheckbox = document.getElementById('remember');

    // Kiểm tra xem có thông tin đăng nhập đã lưu không
    const savedEmail = localStorage.getItem('rememberedEmail');
    const savedPassword = localStorage.getItem('rememberedPassword');
    if (savedEmail && savedPassword) {
        emailInput.value = savedEmail;
        passwordInput.value = savedPassword;
        rememberCheckbox.checked = true;
    }

    // Kiểm tra token từ URL (sau khi đăng nhập bằng Google/Facebook)
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    if (token) {
        localStorage.setItem('token', token);
        window.location.href = '/index.html';
    }

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = emailInput.value.trim();
        const password = passwordInput.value;
        const remember = rememberCheckbox.checked;

        // Hiển thị loading state
        const submitButton = loginForm.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang đăng nhập...';
        submitButton.disabled = true;

        try {
            const response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (response.ok) {
                // Lưu token vào localStorage
                localStorage.setItem('token', data.token);
                
                // Lưu thông tin đăng nhập nếu người dùng chọn "Ghi nhớ đăng nhập"
                if (remember) {
                    localStorage.setItem('rememberedEmail', email);
                    localStorage.setItem('rememberedPassword', password);
                } else {
                    localStorage.removeItem('rememberedEmail');
                    localStorage.removeItem('rememberedPassword');
                }

                // Lưu thông tin người dùng
                localStorage.setItem('user', JSON.stringify(data.user));

                // Hiển thị thông báo thành công
                showMessage('Đăng nhập thành công!', 'success');

                // Chuyển hướng dựa trên vai trò người dùng
                setTimeout(() => {
                    if (data.user.role === 'admin') {
                        window.location.href = '/admin.html';
                    } else {
                        window.location.href = '/index.html';
                    }
                }, 1000);
            } else {
                throw new Error(data.message || 'Đăng nhập thất bại');
            }
        } catch (error) {
            showMessage(error.message, 'error');
        } finally {
            // Khôi phục trạng thái ban đầu của nút
            submitButton.innerHTML = originalButtonText;
            submitButton.disabled = false;
        }
    });

    // Xử lý đăng nhập bằng Google
    document.querySelector('button[data-provider="google"]').addEventListener('click', () => {
        window.location.href = '/api/auth/google';
    });

    // Xử lý đăng nhập bằng Facebook
    document.querySelector('button[data-provider="facebook"]').addEventListener('click', () => {
        window.location.href = '/api/auth/facebook';
    });
});

// Hàm hiển thị thông báo
function showMessage(message, type = 'info') {
    // Tạo thông báo
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    } text-white`;
    
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' : 
                type === 'error' ? 'fa-exclamation-circle' : 
                'fa-info-circle'
            } mr-2"></i>
            <span>${message}</span>
        </div>
    `;

    // Thêm vào DOM
    document.body.appendChild(alertDiv);

    // Tự động xóa sau 3 giây
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
} 