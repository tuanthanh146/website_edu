const express = require('express');
const router = express.Router();
const passport = require('passport');
const Post = require('../models/Post');

// Get all posts with pagination
router.get('/', async (req, res) => {
    try {
        const page = parseInt(req.query.page) || 1;
        const limit = parseInt(req.query.limit) || 10;
        const skip = (page - 1) * limit;

        const posts = await Post.find({ status: 'active' })
            .sort({ createdAt: -1 })
            .skip(skip)
            .limit(limit)
            .populate('author', 'username profile.avatar')
            .populate('likes', 'username')
            .populate('comments.user', 'username profile.avatar');

        const total = await Post.countDocuments({ status: 'active' });

        res.json({
            posts,
            currentPage: page,
            totalPages: Math.ceil(total / limit),
            totalPosts: total
        });
    } catch (error) {
        res.status(500).json({ message: 'Error fetching posts' });
    }
});

// Get most viewed posts
router.get('/popular', async (req, res) => {
    try {
        const posts = await Post.find({ status: 'active' })
            .sort({ views: -1 })
            .limit(3)
            .populate('author', 'username profile.avatar');

        res.json(posts);
    } catch (error) {
        res.status(500).json({ message: 'Error fetching popular posts' });
    }
});

// Create a new post
router.post('/', passport.authenticate('jwt', { session: false }), async (req, res) => {
    try {
        const { title, content, category, tags } = req.body;
        
        const post = new Post({
            title,
            content,
            category,
            tags,
            author: req.user._id
        });

        await post.save();
        res.status(201).json(post);
    } catch (error) {
        res.status(500).json({ message: 'Error creating post' });
    }
});

// Get a single post
router.get('/:id', async (req, res) => {
    try {
        const post = await Post.findById(req.params.id)
            .populate('author', 'username profile.avatar')
            .populate('likes', 'username')
            .populate('comments.user', 'username profile.avatar');

        if (!post) {
            return res.status(404).json({ message: 'Post not found' });
        }

        // Increment view count
        post.views += 1;
        await post.save();

        res.json(post);
    } catch (error) {
        res.status(500).json({ message: 'Error fetching post' });
    }
});

// Add a comment
router.post('/:id/comments', passport.authenticate('jwt', { session: false }), async (req, res) => {
    try {
        const post = await Post.findById(req.params.id);
        if (!post) {
            return res.status(404).json({ message: 'Post not found' });
        }

        post.comments.push({
            user: req.user._id,
            content: req.body.content
        });

        await post.save();
        res.json(post);
    } catch (error) {
        res.status(500).json({ message: 'Error adding comment' });
    }
});

// Like/unlike a post
router.post('/:id/like', passport.authenticate('jwt', { session: false }), async (req, res) => {
    try {
        const post = await Post.findById(req.params.id);
        if (!post) {
            return res.status(404).json({ message: 'Post not found' });
        }

        const likeIndex = post.likes.indexOf(req.user._id);
        if (likeIndex === -1) {
            post.likes.push(req.user._id);
        } else {
            post.likes.splice(likeIndex, 1);
        }

        await post.save();
        res.json(post);
    } catch (error) {
        res.status(500).json({ message: 'Error updating like' });
    }
});

module.exports = router; 