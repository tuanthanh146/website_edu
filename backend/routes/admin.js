const express = require('express');
const router = express.Router();
const { isAdmin } = require('../middleware/auth');

// Get dashboard statistics
router.get('/dashboard', isAdmin, async (req, res) => {
    try {
        // Mock data for now - replace with actual database queries
        const stats = {
            totalPosts: 156,
            totalUsers: 1234,
            todayViews: 2345,
            totalInteractions: 5678
        };
        res.json(stats);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Get recent posts
router.get('/posts', isAdmin, async (req, res) => {
    try {
        // Mock data for now - replace with actual database queries
        const posts = [
            {
                id: 1,
                title: 'Cách giải phương trình bậc hai',
                author: 'Nguyễn Văn A',
                category: 'Toán học',
                views: 2500,
                date: '2024-03-15'
            },
            {
                id: 2,
                title: 'Định luật Newton',
                author: 'Trần Thị B',
                category: 'Vật lý',
                views: 1800,
                date: '2024-03-14'
            }
        ];
        res.json(posts);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Get recent users
router.get('/users', isAdmin, async (req, res) => {
    try {
        // Mock data for now - replace with actual database queries
        const users = [
            {
                id: 1,
                username: 'Nguyễn Văn C',
                email: 'nguyenvanc@example.com',
                role: 'Người dùng',
                joinDate: '2024-03-15'
            },
            {
                id: 2,
                username: 'Lê Thị D',
                email: 'lethid@example.com',
                role: 'Người dùng',
                joinDate: '2024-03-14'
            }
        ];
        res.json(users);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Update post
router.put('/posts/:id', isAdmin, async (req, res) => {
    try {
        const { id } = req.params;
        const { title, content, category } = req.body;
        // Add logic to update post in database
        res.json({ message: 'Post updated successfully' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Delete post
router.delete('/posts/:id', isAdmin, async (req, res) => {
    try {
        const { id } = req.params;
        // Add logic to delete post from database
        res.json({ message: 'Post deleted successfully' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Update user
router.put('/users/:id', isAdmin, async (req, res) => {
    try {
        const { id } = req.params;
        const { username, email, role } = req.body;
        // Add logic to update user in database
        res.json({ message: 'User updated successfully' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Delete user
router.delete('/users/:id', isAdmin, async (req, res) => {
    try {
        const { id } = req.params;
        // Add logic to delete user from database
        res.json({ message: 'User deleted successfully' });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

module.exports = router; 