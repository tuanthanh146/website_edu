const API_BASE_URL = 'https://localhost:5001/api';

export const authService = {
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
            return await response.json();
        } catch (error) {
            console.error('Login error:', error);
            throw error;
        }
    },

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
            return await response.json();
        } catch (error) {
            console.error('Registration error:', error);
            throw error;
        }
    }
}; 