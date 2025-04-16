const express = require('express');
const router = express.Router();

// Basic routes for testing
router.get('/popular', (req, res) => {
    try {
        // Mock data for testing
        const posts = [
            {
                _id: '1',
                title: 'First Post',
                content: 'This is the first post content',
                views: 100
            },
            {
                _id: '2',
                title: 'Second Post',
                content: 'This is the second post content',
                views: 75
            }
        ];
        res.json(posts);
    } catch (error) {
        res.status(500).json({ message: error.message });
    }
});

module.exports = router; 