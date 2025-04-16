const passport = require('passport');
const GoogleStrategy = require('passport-google-oauth20').Strategy;
const User = require('../models/User');
const jwt = require('jsonwebtoken');

passport.serializeUser((user, done) => {
    done(null, user.id);
});

passport.deserializeUser(async (id, done) => {
    try {
        const user = await User.findById(id);
        done(null, user);
    } catch (error) {
        done(error, null);
    }
});

passport.use(new GoogleStrategy({
    clientID: process.env.GOOGLE_CLIENT_ID,
    clientSecret: process.env.GOOGLE_CLIENT_SECRET,
    callbackURL: process.env.CALLBACK_URL
}, async (accessToken, refreshToken, profile, done) => {
    try {
        // Check if user already exists
        let user = await User.findOne({ googleId: profile.id });

        if (!user) {
            // Check if user exists with the same email
            user = await User.findOne({ email: profile.emails[0].value });

            if (user) {
                // Update existing user with Google ID
                user.googleId = profile.id;
                await user.save();
            } else {
                // Create new user
                user = new User({
                    googleId: profile.id,
                    username: profile.displayName,
                    email: profile.emails[0].value,
                    profile: {
                        avatar: profile.photos[0].value
                    }
                });
                await user.save();
            }
        }

        return done(null, user);
    } catch (error) {
        return done(error, null);
    }
}));

// JWT Strategy
const JwtStrategy = require('passport-jwt').Strategy;
const ExtractJwt = require('passport-jwt').ExtractJwt;

const opts = {
    jwtFromRequest: ExtractJwt.fromAuthHeaderAsBearerToken(),
    secretOrKey: process.env.JWT_SECRET
};

passport.use(new JwtStrategy(opts, async (jwt_payload, done) => {
    try {
        const user = await User.findById(jwt_payload.userId);
        if (user) {
            return done(null, user);
        }
        return done(null, false);
    } catch (error) {
        return done(error, false);
    }
})); 