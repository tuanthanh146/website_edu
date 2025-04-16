require('dotenv').config();
const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');
const passport = require('passport');
const session = require('express-session');
const path = require('path');

const app = express();

// CORS configuration
const corsOptions = {
    origin: [process.env.FRONTEND_URL, 'http://localhost:5500'],
    credentials: true,
    methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization']
};
app.use(cors(corsOptions));

// Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Session configuration
app.use(session({
    secret: process.env.SESSION_SECRET || 'your-secret-key',
    resave: false,
    saveUninitialized: false,
    cookie: {
        secure: false, // Set to true in production with HTTPS
        maxAge: 24 * 60 * 60 * 1000, // 24 hours
        httpOnly: true,
        sameSite: 'lax'
    }
}));

// Initialize passport
app.use(passport.initialize());
app.use(passport.session());

// Import passport configuration
require('./config/passport');

// Serve static files from the frontend directory
app.use(express.static(path.join(__dirname, '../frontend')));

// Test route
app.get('/api/test', (req, res) => {
    res.json({ message: 'Backend server is running!' });
});

// Routes
app.use('/api/auth', require('./routes/auth'));
app.use('/api/forum', require('./routes/forum'));
app.use('/api/ai-tools', require('./routes/aiTools'));
app.use('/api/admin', require('./routes/admin'));

// Error handling middleware
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({ error: err.message });
});

// Connect to MongoDB
const connectDB = async () => {
    try {
        await mongoose.connect(process.env.MONGODB_URI || 'mongodb://localhost:27017/eduai');
        console.log('MongoDB connected successfully');
    } catch (error) {
        console.error('MongoDB connection error:', error);
    }
};

// Start server
const startServer = async () => {
    await connectDB();
    const PORT = process.env.PORT || 3000;
    app.listen(PORT, () => {
        console.log(`Server is running on port ${PORT}`);
    });
};

startServer(); 