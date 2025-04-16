<?php
session_start();
require_once __DIR__ . '/../../backend/config/database.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduAI Blog - Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/WEBSITE_EDUAI/assets/css/style.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            color: white;
            padding: 100px 0;
            margin-bottom: 50px;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('/WEBSITE_EDUAI/assets/images/pattern.png') repeat;
            opacity: 0.1;
        }
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .hero-subtitle {
            font-size: 1.5rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        .hero-buttons .btn {
            margin: 10px;
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .btn-get-started {
            background-color: #fff;
            color: #0d6efd;
            border: 2px solid #fff;
        }
        .btn-get-started:hover {
            background-color: transparent;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-explore {
            background-color: transparent;
            color: #fff;
            border: 2px solid #fff;
        }
        .btn-explore:hover {
            background-color: #fff;
            color: #0d6efd;
            transform: translateY(-2px);
        }
        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 40px;
        }
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: #0d6efd;
            border-radius: 3px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .card-img-top {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            height: 200px;
            object-fit: cover;
        }
        .card-body {
            padding: 1.5rem;
        }
        .card-title {
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .card .btn-primary {
            border-radius: 30px;
            padding: 8px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .card .btn-primary:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../components/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Chào mừng đến với EduAI Blog</h1>
                <p class="hero-subtitle">Khám phá thế giới tri thức cùng cộng đồng học tập của chúng tôi</p>
                <div class="hero-buttons">
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="/WEBSITE_EDUAI/register" class="btn btn-get-started">
                            <i class="fas fa-rocket me-2"></i>Bắt đầu ngay
                        </a>
                    <?php else: ?>
                        <a href="/WEBSITE_EDUAI/create-post" class="btn btn-get-started">
                            <i class="fas fa-pen-to-square me-2"></i>Tạo bài viết
                        </a>
                    <?php endif; ?>
                    <a href="/WEBSITE_EDUAI/posts" class="btn btn-explore">
                        <i class="fas fa-book-open me-2"></i>Khám phá bài viết
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Posts Section -->
    <section class="container mb-5">
        <h2 class="text-center section-title">Bài viết nổi bật</h2>
        <div class="row" id="featuredPosts">
            <!-- Featured posts will be loaded here via JavaScript -->
        </div>
    </section>

    <!-- Latest Posts Section -->
    <section class="container mb-5">
        <h2 class="text-center section-title">Bài viết mới nhất</h2>
        <div class="row" id="latestPosts">
            <!-- Latest posts will be loaded here via JavaScript -->
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to format date
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('vi-VN', options);
        }

        // Function to load featured posts
        async function loadFeaturedPosts() {
            try {
                const response = await fetch('/WEBSITE_EDUAI/api/posts/featured');
                const data = await response.json();
                const container = document.getElementById('featuredPosts');
                
                data.forEach(post => {
                    container.innerHTML += `
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <img src="${post.image_url || '/WEBSITE_EDUAI/assets/images/default-post.jpg'}" class="card-img-top" alt="${post.title}">
                                <div class="card-body">
                                    <h5 class="card-title">${post.title}</h5>
                                    <p class="card-text text-muted mb-2">
                                        <small>
                                            <i class="fas fa-calendar-alt me-2"></i>${formatDate(post.created_at)}
                                        </small>
                                    </p>
                                    <p class="card-text">${post.excerpt}</p>
                                    <a href="/WEBSITE_EDUAI/post/${post.id}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right me-2"></i>Đọc thêm
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } catch (error) {
                console.error('Error loading featured posts:', error);
            }
        }

        // Function to load latest posts
        async function loadLatestPosts() {
            try {
                const response = await fetch('/WEBSITE_EDUAI/api/posts/latest');
                const data = await response.json();
                const container = document.getElementById('latestPosts');
                
                data.forEach(post => {
                    container.innerHTML += `
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <img src="${post.image_url || '/WEBSITE_EDUAI/assets/images/default-post.jpg'}" class="card-img-top" alt="${post.title}">
                                <div class="card-body">
                                    <h5 class="card-title">${post.title}</h5>
                                    <p class="card-text text-muted mb-2">
                                        <small>
                                            <i class="fas fa-calendar-alt me-2"></i>${formatDate(post.created_at)}
                                        </small>
                                    </p>
                                    <p class="card-text">${post.excerpt}</p>
                                    <a href="/WEBSITE_EDUAI/post/${post.id}" class="btn btn-primary">
                                        <i class="fas fa-arrow-right me-2"></i>Đọc thêm
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
            } catch (error) {
                console.error('Error loading latest posts:', error);
            }
        }

        // Load posts when page loads
        document.addEventListener('DOMContentLoaded', () => {
            loadFeaturedPosts();
            loadLatestPosts();
        });
    </script>
</body>
</html> 