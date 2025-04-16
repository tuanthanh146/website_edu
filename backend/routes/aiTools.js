const express = require('express');
const router = express.Router();

// Basic routes for testing
router.get('/', (req, res) => {
    try {
        const tools = [
            {
                id: '1',
                name: 'Text Generator',
                description: 'Generate creative and educational content'
            },
            {
                id: '2',
                name: 'Image Generator',
                description: 'Create educational images and illustrations'
            }
        ];
        res.json(tools);
    } catch (error) {
        res.status(500).json({ message: error.message });
    }
});

module.exports = router; 