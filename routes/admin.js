const express = require('express');
const router = express.Router();
const passport = require('passport');
const User = require('../models/User');
const Post = require('../models/Post');

// Middleware to check if user is admin
const isAdmin = (req, res, next) => {
    if (req.user && req.user.role === 'admin') {
        next();
    } else {
        res.status(403).json({ message: 'Access denied' });
    }
};

// Get all users
router.get('/users', passport.authenticate('jwt', { session: false }), isAdmin, async (req, res) => {
    try {
        const users = await User.find().select('-password');
        res.json(users);
    } catch (error) {
        res.status(500).json({ message: 'Error fetching users' });
    }
});

// Update user role
router.put('/users/:id/role', passport.authenticate('jwt', { session: false }), isAdmin, async (req, res) => {
    try {
        const { role } = req.body;
        const user = await User.findByIdAndUpdate(
            req.params.id,
            { role },
            { new: true }
        ).select('-password');

        if (!user) {
            return res.status(404).json({ message: 'User not found' });
        }

        res.json(user);
    } catch (error) {
        res.status(500).json({ message: 'Error updating user role' });
    }
});

// Get all posts (including inactive ones)
router.get('/posts', passport.authenticate('jwt', { session: false }), isAdmin, async (req, res) => {
    try {
        const posts = await Post.find()
            .populate('author', 'username')
            .sort({ createdAt: -1 });
        res.json(posts);
    } catch (error) {
        res.status(500).json({ message: 'Error fetching posts' });
    }
});

// Update post status
router.put('/posts/:id/status', passport.authenticate('jwt', { session: false }), isAdmin, async (req, res) => {
    try {
        const { status } = req.body;
        const post = await Post.findByIdAndUpdate(
            req.params.id,
            { status },
            { new: true }
        ).populate('author', 'username');

        if (!post) {
            return res.status(404).json({ message: 'Post not found' });
        }

        res.json(post);
    } catch (error) {
        res.status(500).json({ message: 'Error updating post status' });
    }
});

// Pin/unpin a post
router.put('/posts/:id/pin', passport.authenticate('jwt', { session: false }), isAdmin, async (req, res) => {
    try {
        const post = await Post.findById(req.params.id);
        if (!post) {
            return res.status(404).json({ message: 'Post not found' });
        }

        post.isPinned = !post.isPinned;
        await post.save();

        res.json(post);
    } catch (error) {
        res.status(500).json({ message: 'Error updating post pin status' });
    }
});

// Delete a post
router.delete('/posts/:id', passport.authenticate('jwt', { session: false }), isAdmin, async (req, res) => {
    try {
        const post = await Post.findByIdAndDelete(req.params.id);
        if (!post) {
            return res.status(404).json({ message: 'Post not found' });
        }

        res.json({ message: 'Post deleted successfully' });
    } catch (error) {
        res.status(500).json({ message: 'Error deleting post' });
    }
});

// Get site statistics
router.get('/stats', passport.authenticate('jwt', { session: false }), isAdmin, async (req, res) => {
    try {
        const totalUsers = await User.countDocuments();
        const totalPosts = await Post.countDocuments();
        const activePosts = await Post.countDocuments({ status: 'active' });
        const totalComments = await Post.aggregate([
            { $unwind: '$comments' },
            { $count: 'totalComments' }
        ]);

        res.json({
            totalUsers,
            totalPosts,
            activePosts,
            totalComments: totalComments[0]?.totalComments || 0
        });
    } catch (error) {
        res.status(500).json({ message: 'Error fetching statistics' });
    }
});

module.exports = router; 