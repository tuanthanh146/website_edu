const API_BASE_URL = 'https://localhost:5001/api';

class AuthService {
    constructor() {
        this.token = localStorage.getItem('token');
        this.user = JSON.parse(localStorage.getItem('user'));
    }

    async login(email, password) {
        try {
            const response = await fetch(`${API_BASE_URL}/auth/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, password }),
                credentials: 'include'
            });

            const data = await response.json();
            
            if (data.success) {
                if (data.requiresTwoFactor) {
                    return { requiresTwoFactor: true, user: data.user };
                }

                this.token = data.token;
                this.user = data.user;
                
                localStorage.setItem('token', data.token);
                localStorage.setItem('user', JSON.stringify(data.user));
                
                return { success: true };
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            throw error;
        }
    }

    async verifyTwoFactor(user, code) {
        try {
            const response = await fetch(`${API_BASE_URL}/auth/verify-2fa`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ user, code }),
                credentials: 'include'
            });

            const data = await response.json();
            
            if (data.success) {
                this.token = data.token;
                this.user = data.user;
                
                localStorage.setItem('token', data.token);
                localStorage.setItem('user', JSON.stringify(data.user));
                
                return { success: true };
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            throw error;
        }
    }

    async register(email, password, fullName) {
        try {
            const response = await fetch(`${API_BASE_URL}/auth/register`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, password, fullName }),
                credentials: 'include'
            });

            const data = await response.json();
            
            if (data.success) {
                return { success: true, message: data.message };
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            throw error;
        }
    }

    async forgotPassword(email) {
        try {
            const response = await fetch(`${API_BASE_URL}/auth/forgot-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email }),
                credentials: 'include'
            });

            const data = await response.json();
            
            if (data.success) {
                return { success: true, message: data.message };
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            throw error;
        }
    }

    async resetPassword(email, token, newPassword) {
        try {
            const response = await fetch(`${API_BASE_URL}/auth/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, token, newPassword }),
                credentials: 'include'
            });

            const data = await response.json();
            
            if (data.success) {
                return { success: true, message: data.message };
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            throw error;
        }
    }

    logout() {
        this.token = null;
        this.user = null;
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login.html';
    }

    isAuthenticated() {
        return !!this.token;
    }

    getCurrentUser() {
        return this.user;
    }

    getToken() {
        return this.token;
    }
}

const authService = new AuthService();
export default authService; 