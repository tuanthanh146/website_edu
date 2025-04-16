const express = require('express');
const router = express.Router();
const passport = require('passport');
const jwt = require('jsonwebtoken');
const User = require('../models/User');

// Google OAuth routes
router.get('/google',
    passport.authenticate('google', { scope: ['profile', 'email'] })
);

router.get('/google/callback',
    passport.authenticate('google', { failureRedirect: '/login' }),
    (req, res) => {
        const token = jwt.sign(
            { userId: req.user._id },
            process.env.JWT_SECRET,
            { expiresIn: '24h' }
        );
        res.redirect(`/auth/success?token=${token}`);
    }
);

// Local registration
router.post('/register', async (req, res) => {
    try {
        const { username, email, password } = req.body;
        
        const existingUser = await User.findOne({ $or: [{ email }, { username }] });
        if (existingUser) {
            return res.status(400).json({ message: 'User already exists' });
        }

        const user = new User({
            username,
            email,
            password
        });

        await user.save();
        
        const token = jwt.sign(
            { userId: user._id },
            process.env.JWT_SECRET,
            { expiresIn: '24h' }
        );

        res.status(201).json({ token, user: { id: user._id, username, email } });
    } catch (error) {
        res.status(500).json({ message: 'Error creating user' });
    }
});

// Local login
router.post('/login', async (req, res) => {
    try {
        const { email, password } = req.body;
        
        const user = await User.findOne({ email });
        if (!user) {
            return res.status(401).json({ message: 'Invalid credentials' });
        }

        const isMatch = await user.comparePassword(password);
        if (!isMatch) {
            return res.status(401).json({ message: 'Invalid credentials' });
        }

        const token = jwt.sign(
            { userId: user._id },
            process.env.JWT_SECRET,
            { expiresIn: '24h' }
        );

        res.json({ token, user: { id: user._id, username: user.username, email: user.email } });
    } catch (error) {
        res.status(500).json({ message: 'Error logging in' });
    }
});

// Get current user
router.get('/me', passport.authenticate('jwt', { session: false }), (req, res) => {
    res.json(req.user);
});

module.exports = router; 