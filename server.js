require('dotenv').config();
const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');
const passport = require('passport');
const session = require('express-session');
const path = require('path');

const app = express();

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));

// Session configuration
app.use(session({
    secret: process.env.JWT_SECRET,
    resave: false,
    saveUninitialized: false
}));

// Passport middleware
app.use(passport.initialize());
app.use(passport.session());

// Database connection
mongoose.connect(process.env.MONGODB_URI)
    .then(() => console.log('Connected to MongoDB'))
    .catch(err => console.error('MongoDB connection error:', err));

// Routes
const authRoutes = require('./routes/auth');
const forumRoutes = require('./routes/forum');
const aiToolsRoutes = require('./routes/aiTools');
const adminRoutes = require('./routes/admin');

app.use('/api/auth', authRoutes);
app.use('/api/forum', forumRoutes);
app.use('/api/ai-tools', aiToolsRoutes);
app.use('/api/admin', adminRoutes);

// Error handling middleware
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({ message: 'Something went wrong!' });
});

const PORT = process.env.PORT || 4000;
app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});