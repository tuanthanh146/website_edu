/**
 * EduAI Admin Login Panel
 */
document.addEventListener('DOMContentLoaded', () => {
    // Khởi tạo login panel
    initAdminLoginPanel();
});

/**
 * Khởi tạo login panel
 */
function initAdminLoginPanel() {
    // Kiểm tra trạng thái đăng nhập
    checkLoginStatus();
    
    // Lắng nghe sự kiện từ form login
    const loginForm = document.getElementById('admin-login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    // Nút đăng xuất
    const logoutBtn = document.getElementById('admin-logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', handleLogout);
    }
}

/**
 * Kiểm tra trạng thái đăng nhập và hiển thị giao diện phù hợp
 */
function checkLoginStatus() {
    const adminPanel = document.querySelector('.admin-panel');
    if (!adminPanel) return;
    
    const loginPanel = document.getElementById('admin-login-panel');
    const dashboardPanel = document.getElementById('admin-dashboard-panel');
    
    if (AuthService.isLoggedIn() && AuthService.isAdmin()) {
        // Đã đăng nhập và là admin
        if (loginPanel) loginPanel.classList.add('hidden');
        if (dashboardPanel) {
            dashboardPanel.classList.remove('hidden');
            updateUserInfo();
        }
    } else {
        // Chưa đăng nhập hoặc không phải admin
        if (dashboardPanel) dashboardPanel.classList.add('hidden');
        if (loginPanel) {
            loginPanel.classList.remove('hidden');
            
            // Kiểm tra thông tin đăng nhập đã lưu
            const savedCredentials = AuthService.getRememberedCredentials();
            if (savedCredentials) {
                const emailInput = document.getElementById('admin-email');
                const passwordInput = document.getElementById('admin-password');
                const rememberCheckbox = document.getElementById('admin-remember');
                
                if (emailInput) emailInput.value = savedCredentials.email;
                if (passwordInput) passwordInput.value = savedCredentials.password;
                if (rememberCheckbox) rememberCheckbox.checked = true;
            }
        }
    }
}

/**
 * Xử lý đăng nhập
 * @param {Event} e - Sự kiện submit form
 */
async function handleLogin(e) {
    e.preventDefault();
    
    const emailInput = document.getElementById('admin-email');
    const passwordInput = document.getElementById('admin-password');
    const rememberCheckbox = document.getElementById('admin-remember');
    const submitButton = document.querySelector('#admin-login-form button[type="submit"]');
    
    if (!emailInput || !passwordInput) return;
    
    const email = emailInput.value.trim();
    const password = passwordInput.value;
    const remember = rememberCheckbox?.checked || false;
    
    if (!email || !password) {
        showMessage('Vui lòng nhập đầy đủ thông tin', 'error');
        return;
    }
    
    // Đổi trạng thái nút submit
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Đang đăng nhập...';
    submitButton.disabled = true;
    
    try {
        // Đăng nhập 
        // Sử dụng mockLogin cho demo, sản phẩm thật sẽ dùng login
        const result = await AuthService.mockLogin(email, password, remember);
        
        if (result.success) {
            showMessage('Đăng nhập thành công!', 'success');
            
            // Kiểm tra vai trò
            if (!AuthService.isAdmin()) {
                showMessage('Tài khoản không có quyền truy cập khu vực này', 'error');
                await AuthService.logout();
            } else {
                // Cập nhật giao diện
                checkLoginStatus();
            }
        } else {
            showMessage('Đăng nhập thất bại: ' + (result.message || 'Lỗi không xác định'), 'error');
        }
    } catch (error) {
        showMessage('Đăng nhập thất bại: ' + error.message, 'error');
    } finally {
        // Khôi phục nút submit
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }
}

/**
 * Xử lý đăng xuất
 */
async function handleLogout() {
    try {
        const result = await AuthService.logout();
        if (result) {
            showMessage('Đã đăng xuất thành công', 'success');
            checkLoginStatus();
        } else {
            showMessage('Đăng xuất thất bại', 'error');
        }
    } catch (error) {
        showMessage('Đăng xuất thất bại: ' + error.message, 'error');
    }
}

/**
 * Cập nhật thông tin người dùng trên giao diện
 */
function updateUserInfo() {
    const user = AuthService.getUser();
    if (!user) return;
    
    // Cập nhật tên người dùng
    const userNameElements = document.querySelectorAll('.admin-user-name');
    userNameElements.forEach(el => {
        el.textContent = user.name || 'Admin';
    });
    
    // Cập nhật avatar
    const userAvatarElements = document.querySelectorAll('.admin-user-avatar');
    userAvatarElements.forEach(el => {
        if (user.avatar) {
            el.src = user.avatar;
            el.alt = user.name || 'Admin';
        }
    });
    
    // Thêm logic khác nếu cần
}

/**
 * Hiển thị thông báo
 * @param {string} message - Nội dung thông báo
 * @param {string} type - Loại thông báo (success, error, info)
 */
function showMessage(message, type = 'info') {
    // Tạo phần tử thông báo
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
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
    
    // Thêm vào trang
    document.body.appendChild(alertDiv);
    
    // Tự động ẩn sau 3 giây
    setTimeout(() => {
        alertDiv.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
} 