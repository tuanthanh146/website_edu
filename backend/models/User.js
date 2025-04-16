const mongoose = require('mongoose');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');

const userSchema = new mongoose.Schema({
    name: {
        type: String,
        required: true,
        trim: true
    },
    email: {
        type: String,
        required: true,
        unique: true,
        trim: true,
        lowercase: true
    },
    password: {
        type: String,
        required: true,
        minlength: 6
    },
    role: {
        type: String,
        enum: ['user', 'admin'],
        default: 'user'
    },
    avatar: {
        type: String,
        default: 'https://via.placeholder.com/150'
    },
    googleId: String,
    facebookId: String,
    createdAt: {
        type: Date,
        default: Date.now
    },
    lastLogin: {
        type: Date,
        default: Date.now
    }
});

// Hash password trước khi lưu
userSchema.pre('save', async function(next) {
    if (!this.isModified('password')) return next();
    
    try {
        const salt = await bcrypt.genSalt(10);
        this.password = await bcrypt.hash(this.password, salt);
        next();
    } catch (error) {
        next(error);
    }
});

// Phương thức so sánh mật khẩu
userSchema.methods.comparePassword = async function(candidatePassword) {
    return bcrypt.compare(candidatePassword, this.password);
};

// Phương thức tạo token
userSchema.methods.generateAuthToken = function() {
    return jwt.sign(
        { 
            id: this._id,
            email: this.email,
            role: this.role
        },
        process.env.JWT_SECRET || 'your-secret-key',
        { expiresIn: '24h' }
    );
};

const User = mongoose.model('User', userSchema);

module.exports = User; 