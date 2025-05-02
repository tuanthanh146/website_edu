/**
 * EduAI Auth Service - Quản lý xác thực và phiên làm việc
 */

const AuthService = (function() {
    // Các key lưu trữ
    const TOKEN_KEY = 'eduai_token';
    const USER_KEY = 'eduai_user';
    const REMEMBER_EMAIL_KEY = 'eduai_remembered_email';
    const REMEMBER_PASSWORD_KEY = 'eduai_remembered_password';
    
    // API endpoints
    const API_ENDPOINTS = {
        login: '/api/auth/login',
        logout: '/api/auth/logout',
        verify: '/api/auth/verify'
    };
    
    // CSRF Token từ meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    /**
     * Gửi yêu cầu tới API
     * @param {string} endpoint - Đường dẫn API
     * @param {Object} data - Dữ liệu gửi đi
     * @param {string} method - Phương thức HTTP
     * @returns {Promise<any>} - Promise chứa kết quả
     */
    const fetchAPI = async (endpoint, data, method = 'POST') => {
        try {
            const response = await fetch(endpoint, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Authorization': `Bearer ${getToken()}`
                },
                body: data ? JSON.stringify(data) : undefined
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Lỗi kết nối đến máy chủ');
            }

            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    };
    
    /**
     * Lấy token xác thực
     * @returns {string|null} - Token hoặc null nếu chưa đăng nhập
     */
    const getToken = () => {
        return localStorage.getItem(TOKEN_KEY) || sessionStorage.getItem(TOKEN_KEY);
    };
    
    /**
     * Lấy thông tin người dùng
     * @returns {Object|null} - Thông tin người dùng hoặc null
     */
    const getUser = () => {
        const userJson = localStorage.getItem(USER_KEY) || sessionStorage.getItem(USER_KEY);
        return userJson ? JSON.parse(userJson) : null;
    };
    
    /**
     * Lưu thông tin xác thực
     * @param {string} token - Token xác thực
     * @param {Object} user - Thông tin người dùng
     * @param {boolean} remember - Có ghi nhớ không
     */
    const saveAuth = (token, user, remember = false) => {
        const storage = remember ? localStorage : sessionStorage;
        
        storage.setItem(TOKEN_KEY, token);
        storage.setItem(USER_KEY, JSON.stringify(user));
    };
    
    /**
     * Lưu thông tin đăng nhập để ghi nhớ
     * @param {string} email - Email
     * @param {string} password - Mật khẩu
     */
    const saveRememberCredentials = (email, password) => {
        localStorage.setItem(REMEMBER_EMAIL_KEY, email);
        localStorage.setItem(REMEMBER_PASSWORD_KEY, password);
    };
    
    /**
     * Xóa thông tin ghi nhớ đăng nhập
     */
    const clearRememberCredentials = () => {
        localStorage.removeItem(REMEMBER_EMAIL_KEY);
        localStorage.removeItem(REMEMBER_PASSWORD_KEY);
    };
    
    /**
     * Kiểm tra đã đăng nhập chưa
     * @returns {boolean} - Đã đăng nhập hay chưa
     */
    const isLoggedIn = () => {
        return !!getToken();
    };
    
    /**
     * Kiểm tra có phải là Admin không
     * @returns {boolean} - Có phải Admin không
     */
    const isAdmin = () => {
        const user = getUser();
        return user && (user.role === 'admin' || user.role === 'superadmin');
    };
    
    // Public API
    return {
        /**
         * Đăng nhập
         * @param {string} email - Email đăng nhập
         * @param {string} password - Mật khẩu
         * @param {boolean} remember - Ghi nhớ đăng nhập
         * @returns {Promise<Object>} - Kết quả đăng nhập
         */
        login: async function(email, password, remember = false) {
            try {
                const response = await fetchAPI(API_ENDPOINTS.login, { email, password });
                
                if (response.success && response.token) {
                    saveAuth(response.token, response.user, remember);
                    
                    if (remember) {
                        saveRememberCredentials(email, password);
                    } else {
                        clearRememberCredentials();
                    }
                    
                    return {
                        success: true,
                        user: response.user
                    };
                } else {
                    throw new Error(response.message || 'Đăng nhập thất bại');
                }
            } catch (error) {
                console.error('Login Error:', error);
                throw error;
            }
        },
        
        /**
         * Mô phỏng đăng nhập cho demo (không có server)
         * @param {string} email - Email đăng nhập
         * @param {string} password - Mật khẩu
         * @param {boolean} remember - Ghi nhớ đăng nhập
         * @returns {Promise<Object>} - Kết quả đăng nhập
         */
        mockLogin: async function(email, password, remember = false) {
            // Demo với tài khoản admin/admin123 và user/user123
            await new Promise(resolve => setTimeout(resolve, 800)); // Giả lập độ trễ
            
            if (email === 'admin@eduai.com' && password === 'admin123') {
                const userData = {
                    id: 1,
                    name: 'Admin',
                    email: 'admin@eduai.com',
                    role: 'admin',
                    avatar: '/assets/images/avatar.png'
                };
                
                const token = 'mock-token-admin-' + Date.now();
                saveAuth(token, userData, remember);
                
                if (remember) {
                    saveRememberCredentials(email, password);
                } else {
                    clearRememberCredentials();
                }
                
                return {
                    success: true,
                    user: userData
                };
            } else if (email === 'user@eduai.com' && password === 'user123') {
                const userData = {
                    id: 2,
                    name: 'User',
                    email: 'user@eduai.com',
                    role: 'user',
                    avatar: '/assets/images/avatar.png'
                };
                
                const token = 'mock-token-user-' + Date.now();
                saveAuth(token, userData, remember);
                
                if (remember) {
                    saveRememberCredentials(email, password);
                } else {
                    clearRememberCredentials();
                }
                
                return {
                    success: true,
                    user: userData
                };
            } else {
                throw new Error('Email hoặc mật khẩu không chính xác');
            }
        },
        
        /**
         * Đăng xuất
         * @returns {Promise<boolean>} - Kết quả đăng xuất
         */
        logout: async function() {
            try {
                // Gọi API đăng xuất
                // await fetchAPI(API_ENDPOINTS.logout, null, 'POST');
                
                // Xóa dữ liệu xác thực
                localStorage.removeItem(TOKEN_KEY);
                sessionStorage.removeItem(TOKEN_KEY);
                localStorage.removeItem(USER_KEY);
                sessionStorage.removeItem(USER_KEY);
                
                return true;
            } catch (error) {
                console.error('Logout Error:', error);
                return false;
            }
        },
        
        /**
         * Kiểm tra đã đăng nhập chưa
         * @returns {boolean} - Đã đăng nhập hay chưa
         */
        isLoggedIn: isLoggedIn,
        
        /**
         * Kiểm tra có phải là Admin không
         * @returns {boolean} - Có phải Admin không
         */
        isAdmin: isAdmin,
        
        /**
         * Lấy thông tin người dùng
         * @returns {Object|null} - Thông tin người dùng
         */
        getUser: getUser,
        
        /**
         * Lấy thông tin ghi nhớ đăng nhập
         * @returns {Object|null} - Thông tin đăng nhập đã lưu
         */
        getRememberedCredentials: function() {
            const email = localStorage.getItem(REMEMBER_EMAIL_KEY);
            const password = localStorage.getItem(REMEMBER_PASSWORD_KEY);
            
            if (email && password) {
                return { email, password };
            }
            
            return null;
        }
    };
})(); 