// API base URL
const API_BASE_URL = '/backend/api';

// Post functions
const postService = {
    // Get all posts
    async getPosts(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const response = await fetch(`${API_BASE_URL}/posts.php?${queryString}`);
        return await response.json();
    },

    // Create new post
    async createPost(postData) {
        const response = await fetch(`${API_BASE_URL}/posts.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(postData)
        });
        return await response.json();
    },

    // Update post
    async updatePost(postId, postData) {
        const response = await fetch(`${API_BASE_URL}/posts.php?id=${postId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(postData)
        });
        return await response.json();
    },

    // Delete post
    async deletePost(postId) {
        const response = await fetch(`${API_BASE_URL}/posts.php?id=${postId}`, {
            method: 'DELETE'
        });
        return await response.json();
    }
};

// Initialize carousel for most viewed posts
function initMostViewedCarousel() {
    const carousel = document.getElementById('mostViewedCarousel');
    if (!carousel) return;

    postService.getPosts({ order_by: 'views', order: 'DESC', limit: 5 })
        .then(response => {
            if (response.success) {
                const posts = response.data;
                const indicators = carousel.querySelector('.carousel-indicators');
                const inner = carousel.querySelector('.carousel-inner');

                // Clear existing content
                indicators.innerHTML = '';
                inner.innerHTML = '';

                // Add indicators and slides
                posts.forEach((post, index) => {
                    // Add indicator
                    const indicator = document.createElement('button');
                    indicator.type = 'button';
                    indicator.setAttribute('data-bs-target', '#mostViewedCarousel');
                    indicator.setAttribute('data-bs-slide-to', index);
                    if (index === 0) indicator.classList.add('active');
                    indicators.appendChild(indicator);

                    // Add slide
                    const slide = document.createElement('div');
                    slide.className = `carousel-item ${index === 0 ? 'active' : ''}`;
                    slide.innerHTML = `
                        <div class="carousel-caption">
                            <h2>${post.title}</h2>
                            <p class="d-none d-md-block">
                                ${post.content.substring(0, 150)}...
                            </p>
                            <p class="text-muted">
                                Đăng bởi ${post.author_name} 
                                vào ${new Date(post.created_at).toLocaleDateString()}
                            </p>
                            <a href="pages/post-detail.php?id=${post.id}" class="btn btn-primary">Đọc thêm</a>
                        </div>
                    `;
                    inner.appendChild(slide);
                });

                // Initialize Bootstrap carousel
                new bootstrap.Carousel(carousel, {
                    interval: 5000,
                    wrap: true
                });
            }
        })
        .catch(error => console.error('Error loading most viewed posts:', error));
}

// Handle post form submission
function handlePostForm() {
    const form = document.getElementById('postForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const postData = {
            title: formData.get('title'),
            content: formData.get('content'),
            category_id: formData.get('category_id') || null
        };

        try {
            const response = await postService.createPost(postData);
            if (response.success) {
                alert('Bài viết đã được đăng thành công!');
                window.location.href = 'posts.php';
            } else {
                alert('Có lỗi xảy ra khi đăng bài viết: ' + response.message);
            }
        } catch (error) {
            console.error('Error creating post:', error);
            alert('Có lỗi xảy ra khi đăng bài viết');
        }
    });
}

// Initialize post list
function initPostList() {
    const postList = document.getElementById('postList');
    if (!postList) return;

    const categoryId = new URLSearchParams(window.location.search).get('category');
    const params = categoryId ? { category_id: categoryId } : {};

    postService.getPosts(params)
        .then(response => {
            if (response.success) {
                const posts = response.data;
                if (posts.length === 0) {
                    postList.innerHTML = '<div class="alert alert-info">Chưa có bài viết nào.</div>';
                    return;
                }

                postList.innerHTML = posts.map(post => `
                    <div class="card mb-4">
                        <div class="card-body">
                            <h3 class="card-title">${post.title}</h3>
                            <p class="text-muted">
                                Đăng bởi ${post.author_name} 
                                vào ${new Date(post.created_at).toLocaleDateString()}
                                ${post.category_name ? `| Danh mục: ${post.category_name}` : ''}
                            </p>
                            <p class="card-text">
                                ${post.content.substring(0, 200)}...
                            </p>
                            <a href="post-detail.php?id=${post.id}" class="btn btn-primary">Đọc thêm</a>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(error => {
            console.error('Error loading posts:', error);
            postList.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra khi tải bài viết.</div>';
        });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    initMostViewedCarousel();
    handlePostForm();
    initPostList();
}); 