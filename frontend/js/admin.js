// Function to fetch and display dashboard statistics
async function loadDashboardStats() {
    try {
        const response = await fetch('/api/admin/dashboard', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to fetch dashboard stats');
        }
        
        const stats = await response.json();
        
        // Update the statistics cards
        document.getElementById('totalPosts').textContent = stats.totalPosts;
        document.getElementById('totalUsers').textContent = stats.totalUsers;
        document.getElementById('todayViews').textContent = stats.todayViews;
        document.getElementById('totalInteractions').textContent = stats.totalInteractions;
    } catch (error) {
        console.error('Error loading dashboard stats:', error);
        showError('Failed to load dashboard statistics');
    }
}

// Function to fetch and display recent posts
async function loadRecentPosts() {
    try {
        const response = await fetch('/api/admin/posts', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to fetch recent posts');
        }
        
        const posts = await response.json();
        const postsTable = document.getElementById('postsTable');
        
        // Clear existing rows
        postsTable.innerHTML = '';
        
        // Add new rows
        posts.forEach(post => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">${post.title}</td>
                <td class="px-6 py-4 whitespace-nowrap">${post.author}</td>
                <td class="px-6 py-4 whitespace-nowrap">${post.category}</td>
                <td class="px-6 py-4 whitespace-nowrap">${post.views}</td>
                <td class="px-6 py-4 whitespace-nowrap">${post.date}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <button onclick="editPost(${post.id})" class="text-blue-600 hover:text-blue-900 mr-3">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deletePost(${post.id})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            postsTable.appendChild(row);
        });
    } catch (error) {
        console.error('Error loading recent posts:', error);
        showError('Failed to load recent posts');
    }
}

// Function to fetch and display recent users
async function loadRecentUsers() {
    try {
        const response = await fetch('/api/admin/users', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to fetch recent users');
        }
        
        const users = await response.json();
        const usersTable = document.getElementById('usersTable');
        
        // Clear existing rows
        usersTable.innerHTML = '';
        
        // Add new rows
        users.forEach(user => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">${user.username}</td>
                <td class="px-6 py-4 whitespace-nowrap">${user.email}</td>
                <td class="px-6 py-4 whitespace-nowrap">${user.role}</td>
                <td class="px-6 py-4 whitespace-nowrap">${user.joinDate}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <button onclick="editUser(${user.id})" class="text-blue-600 hover:text-blue-900 mr-3">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            usersTable.appendChild(row);
        });
    } catch (error) {
        console.error('Error loading recent users:', error);
        showError('Failed to load recent users');
    }
}

// Function to edit a post
async function editPost(postId) {
    // Implement post editing functionality
    console.log('Editing post:', postId);
}

// Function to delete a post
async function deletePost(postId) {
    if (confirm('Are you sure you want to delete this post?')) {
        try {
            const response = await fetch(`/api/admin/posts/${postId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to delete post');
            }
            
            // Reload the posts table
            loadRecentPosts();
            showSuccess('Post deleted successfully');
        } catch (error) {
            console.error('Error deleting post:', error);
            showError('Failed to delete post');
        }
    }
}

// Function to edit a user
async function editUser(userId) {
    // Implement user editing functionality
    console.log('Editing user:', userId);
}

// Function to delete a user
async function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        try {
            const response = await fetch(`/api/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to delete user');
            }
            
            // Reload the users table
            loadRecentUsers();
            showSuccess('User deleted successfully');
        } catch (error) {
            console.error('Error deleting user:', error);
            showError('Failed to delete user');
        }
    }
}

// Function to show error messages
function showError(message) {
    // Implement error message display
    console.error(message);
}

// Function to show success messages
function showSuccess(message) {
    // Implement success message display
    console.log(message);
}

// Initialize the admin panel when the page loads
document.addEventListener('DOMContentLoaded', () => {
    // Check if user is logged in and is admin
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = '/login.html';
        return;
    }
    
    // Load all data
    loadDashboardStats();
    loadRecentPosts();
    loadRecentUsers();
}); 