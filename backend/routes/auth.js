const express = require('express');
const router = express.Router();
const passport = require('passport');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');
const User = require('../models/User');

// Basic routes for testing
router.post('/register', async (req, res) => {
    try {
        res.json({ message: 'Registration endpoint' });
    } catch (error) {
        res.status(500).json({ message: error.message });
    }
});

// Đăng nhập
router.post('/login', async (req, res) => {
    try {
        const { email, password } = req.body;

        // Kiểm tra email
        const user = await User.findOne({ email });
        if (!user) {
            return res.status(400).json({ message: 'Email hoặc mật khẩu không đúng' });
        }

        // Kiểm tra mật khẩu
        const isMatch = await bcrypt.compare(password, user.password);
        if (!isMatch) {
            return res.status(400).json({ message: 'Email hoặc mật khẩu không đúng' });
        }

        // Tạo token
        const token = jwt.sign(
            { 
                id: user._id,
                email: user.email,
                role: user.role
            },
            process.env.JWT_SECRET || 'your-secret-key',
            { expiresIn: '24h' }
        );

        // Trả về thông tin người dùng và token
        res.json({
            token,
            user: {
                id: user._id,
                email: user.email,
                name: user.name,
                role: user.role
            }
        });
    } catch (error) {
        console.error('Login error:', error);
        res.status(500).json({ message: 'Lỗi server' });
    }
});

// Đăng nhập bằng Google
router.get('/google',
    passport.authenticate('google', {
        scope: ['profile', 'email'],
        session: false
    })
);

router.get('/google/callback',
    passport.authenticate('google', {
        failureRedirect: '/login.html',
        session: false
    }),
    (req, res) => {
        // Tạo token cho người dùng
        const token = jwt.sign(
            { 
                id: req.user._id,
                email: req.user.email,
                role: req.user.role
            },
            process.env.JWT_SECRET || 'your-secret-key',
            { expiresIn: '24h' }
        );

        // Chuyển hướng về trang chủ với token
        res.redirect(`${process.env.FRONTEND_URL || 'http://localhost:5500'}/index.html?token=${token}`);
    }
);

// Đăng nhập bằng Facebook
router.get('/facebook',
    passport.authenticate('facebook', {
        scope: ['email'],
        session: false
    })
);

router.get('/facebook/callback',
    passport.authenticate('facebook', {
        failureRedirect: '/login.html',
        session: false
    }),
    (req, res) => {
        // Tạo token cho người dùng
        const token = jwt.sign(
            { 
                id: req.user._id,
                email: req.user.email,
                role: req.user.role
            },
            process.env.JWT_SECRET || 'your-secret-key',
            { expiresIn: '24h' }
        );

        // Chuyển hướng về trang chủ với token
        res.redirect(`${process.env.FRONTEND_URL || 'http://localhost:5500'}/index.html?token=${token}`);
    }
);

// Đăng xuất
router.post('/logout', (req, res) => {
    res.json({ message: 'Đăng xuất thành công' });
});

router.get('/me', (req, res) => {
    try {
        res.json({ message: 'User profile endpoint' });
    } catch (error) {
        res.status(500).json({ message: error.message });
    }
});

module.exports = router; 