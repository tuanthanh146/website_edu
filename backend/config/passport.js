const passport = require('passport');
const GoogleStrategy = require('passport-google-oauth20').Strategy;
const FacebookStrategy = require('passport-facebook').Strategy;
const User = require('../models/User');

// Serialize user
passport.serializeUser((user, done) => {
    done(null, user.id);
});

// Deserialize user
passport.deserializeUser(async (id, done) => {
    try {
        const user = await User.findById(id);
        done(null, user);
    } catch (error) {
        done(error, null);
    }
});

// Google Strategy
passport.use(new GoogleStrategy({
    clientID: process.env.GOOGLE_CLIENT_ID,
    clientSecret: process.env.GOOGLE_CLIENT_SECRET,
    callbackURL: "/api/auth/google/callback"
}, async (accessToken, refreshToken, profile, done) => {
    try {
        // Kiểm tra xem người dùng đã tồn tại chưa
        let user = await User.findOne({ googleId: profile.id });

        if (!user) {
            // Kiểm tra xem email đã được sử dụng chưa
            user = await User.findOne({ email: profile.emails[0].value });

            if (user) {
                // Nếu email đã tồn tại, cập nhật googleId
                user.googleId = profile.id;
                await user.save();
            } else {
                // Tạo người dùng mới
                user = await User.create({
                    name: profile.displayName,
                    email: profile.emails[0].value,
                    googleId: profile.id,
                    avatar: profile.photos[0].value,
                    password: Math.random().toString(36).slice(-8) // Tạo mật khẩu ngẫu nhiên
                });
            }
        }

        // Cập nhật thời gian đăng nhập cuối cùng
        user.lastLogin = new Date();
        await user.save();

        return done(null, user);
    } catch (error) {
        return done(error, null);
    }
}));

// Facebook Strategy
passport.use(new FacebookStrategy({
    clientID: process.env.FACEBOOK_APP_ID,
    clientSecret: process.env.FACEBOOK_APP_SECRET,
    callbackURL: "/api/auth/facebook/callback",
    profileFields: ['id', 'emails', 'name', 'picture.type(large)']
}, async (accessToken, refreshToken, profile, done) => {
    try {
        // Kiểm tra xem người dùng đã tồn tại chưa
        let user = await User.findOne({ facebookId: profile.id });

        if (!user) {
            // Kiểm tra xem email đã được sử dụng chưa
            user = await User.findOne({ email: profile.emails[0].value });

            if (user) {
                // Nếu email đã tồn tại, cập nhật facebookId
                user.facebookId = profile.id;
                await user.save();
            } else {
                // Tạo người dùng mới
                user = await User.create({
                    name: `${profile.name.givenName} ${profile.name.familyName}`,
                    email: profile.emails[0].value,
                    facebookId: profile.id,
                    avatar: profile.photos[0].value,
                    password: Math.random().toString(36).slice(-8) // Tạo mật khẩu ngẫu nhiên
                });
            }
        }

        // Cập nhật thời gian đăng nhập cuối cùng
        user.lastLogin = new Date();
        await user.save();

        return done(null, user);
    } catch (error) {
        return done(error, null);
    }
})); 