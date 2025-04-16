# EduAI Platform

A comprehensive educational platform powered by AI, featuring a forum, various AI tools, and user management system.

## Features

- User Authentication (Local & Google OAuth)
- Forum with posts, comments, and likes
- Multiple AI Tools:
  - Text Generator
  - Image Generator
  - Code Assistant
  - Text Summarizer
  - Translator
- Admin Panel for content and user management
- Responsive design with Tailwind CSS
- RESTful API architecture

## Tech Stack

- Backend:
  - Node.js
  - Express.js
  - MongoDB
  - Passport.js (Authentication)
  - JWT (JSON Web Tokens)

- Frontend:
  - HTML5
  - Tailwind CSS
  - JavaScript (Vanilla)
  - Font Awesome Icons

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/eduai-platform.git
cd eduai-platform
```

2. Install dependencies:
```bash
npm install
```

3. Create a `.env` file in the root directory with the following variables:
```
PORT=3000
MONGODB_URI=mongodb://localhost:27017/eduai
JWT_SECRET=your_jwt_secret_key
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
CALLBACK_URL=http://localhost:3000/auth/google/callback
```

4. Start the development server:
```bash
npm run dev
```

## Project Structure

```
eduai-platform/
├── config/
│   └── passport.js
├── models/
│   ├── User.js
│   └── Post.js
├── routes/
│   ├── auth.js
│   ├── forum.js
│   ├── aiTools.js
│   └── admin.js
├── public/
│   ├── index.html
│   ├── login.html
│   ├── register.html
│   ├── forum.html
│   └── ai-tools.html
├── .env
├── package.json
├── server.js
└── README.md
```

## API Endpoints

### Authentication
- POST /api/auth/register - Register a new user
- POST /api/auth/login - Login user
- GET /api/auth/google - Google OAuth login
- GET /api/auth/me - Get current user

### Forum
- GET /api/forum - Get all posts
- GET /api/forum/popular - Get most viewed posts
- POST /api/forum - Create a new post
- GET /api/forum/:id - Get a single post
- POST /api/forum/:id/comments - Add a comment
- POST /api/forum/:id/like - Like/unlike a post

### AI Tools
- GET /api/ai-tools/available-tools - Get list of available tools
- POST /api/ai-tools/text-generator - Generate text
- POST /api/ai-tools/image-generator - Generate image
- POST /api/ai-tools/code-completion - Complete code
- POST /api/ai-tools/text-summarizer - Summarize text
- POST /api/ai-tools/translator - Translate text

### Admin
- GET /api/admin/users - Get all users
- PUT /api/admin/users/:id/role - Update user role
- GET /api/admin/posts - Get all posts
- PUT /api/admin/posts/:id/status - Update post status
- PUT /api/admin/posts/:id/pin - Pin/unpin post
- DELETE /api/admin/posts/:id - Delete post
- GET /api/admin/stats - Get site statistics

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 