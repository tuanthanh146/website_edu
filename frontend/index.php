<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDUAI Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .post-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .post-card:hover {
            transform: translateY(-5px);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
        .carousel-item img {
            height: 400px;
            object-fit: cover;
        }
        .carousel-caption {
            background: rgba(0,0,0,0.5);
            border-radius: 10px;
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/WEBSITE_EDUAI">EDUAI Blog</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/WEBSITE_EDUAI">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/WEBSITE_EDUAI/posts">Posts</a>
                    </li>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/WEBSITE_EDUAI/admin">Admin</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <img src="<?php echo $_SESSION['user_picture'] ?? 'https://via.placeholder.com/32'; ?>" 
                                     alt="User" class="user-avatar me-2">
                                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/WEBSITE_EDUAI/dashboard">Dashboard</a></li>
                                <li><a class="dropdown-item" href="/WEBSITE_EDUAI/create-post">Create Post</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/WEBSITE_EDUAI/logout">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/WEBSITE_EDUAI/login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/WEBSITE_EDUAI/register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Featured Posts Carousel -->
        <div id="featuredCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
            <div class="carousel-inner" id="featuredPosts">
                <!-- Posts will be loaded here by JavaScript -->
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        <!-- Latest Posts -->
        <div class="row">
            <div class="col-md-8">
                <h2 class="mb-4">Latest Posts</h2>
                <div id="latestPosts" class="row">
                    <!-- Posts will be loaded here by JavaScript -->
                </div>
                <div class="text-center mt-4 mb-4">
                    <button id="loadMore" class="btn btn-primary">Load More</button>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Categories</h5>
                    </div>
                    <div class="card-body">
                        <div id="categories" class="list-group">
                            <!-- Categories will be loaded here by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load featured posts
        fetch('/WEBSITE_EDUAI/api/posts?limit=5')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.posts.length > 0) {
                    const carousel = document.getElementById('featuredPosts');
                    data.data.posts.forEach((post, index) => {
                        const div = document.createElement('div');
                        div.className = `carousel-item ${index === 0 ? 'active' : ''}`;
                        div.innerHTML = `
                            <img src="${post.featured_image || 'https://via.placeholder.com/800x400'}" class="d-block w-100" alt="${post.title}">
                            <div class="carousel-caption">
                                <h3>${post.title}</h3>
                                <p class="d-none d-md-block">${post.content.substring(0, 150)}...</p>
                                <a href="/WEBSITE_EDUAI/post/${post.id}" class="btn btn-primary">Read More</a>
                            </div>
                        `;
                        carousel.appendChild(div);
                    });
                }
            });

        // Load latest posts
        let currentPage = 1;
        function loadPosts() {
            fetch(`/WEBSITE_EDUAI/api/posts?page=${currentPage}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.posts.length > 0) {
                        const container = document.getElementById('latestPosts');
                        data.data.posts.forEach(post => {
                            const div = document.createElement('div');
                            div.className = 'col-md-6 mb-4';
                            div.innerHTML = `
                                <div class="card post-card">
                                    <img src="${post.featured_image || 'https://via.placeholder.com/400x200'}" class="card-img-top" alt="${post.title}">
                                    <div class="card-body">
                                        <h5 class="card-title">${post.title}</h5>
                                        <p class="card-text">${post.content.substring(0, 100)}...</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">By ${post.author_name}</small>
                                            <a href="/WEBSITE_EDUAI/post/${post.id}" class="btn btn-sm btn-primary">Read More</a>
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.appendChild(div);
                        });

                        if (currentPage >= data.data.pagination.total_pages) {
                            document.getElementById('loadMore').style.display = 'none';
                        }
                    }
                });
        }

        // Load categories
        fetch('/WEBSITE_EDUAI/api/categories')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.categories.length > 0) {
                    const container = document.getElementById('categories');
                    data.data.categories.forEach(category => {
                        const a = document.createElement('a');
                        a.href = `/WEBSITE_EDUAI/posts?category=${category.slug}`;
                        a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                        a.innerHTML = `
                            ${category.name}
                            <span class="badge bg-primary rounded-pill">0</span>
                        `;
                        container.appendChild(a);
                    });
                }
            });

        // Load initial posts
        loadPosts();

        // Load more posts
        document.getElementById('loadMore').addEventListener('click', () => {
            currentPage++;
            loadPosts();
        });
    </script>
</body>
</html> 