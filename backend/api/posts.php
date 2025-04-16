<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../middleware/auth.php';

// Get all posts
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $category_id = $_GET['category_id'] ?? null;
    $limit = $_GET['limit'] ?? null;
    $order_by = $_GET['order_by'] ?? 'created_at';
    $order = $_GET['order'] ?? 'DESC';

    $query = "
        SELECT p.*, u.name as author_name, c.name as category_name 
        FROM posts p 
        LEFT JOIN users u ON p.author_id = u.id 
        LEFT JOIN categories c ON p.category_id = c.id 
    ";

    $params = [];
    if ($category_id) {
        $query .= " WHERE p.category_id = ?";
        $params[] = $category_id;
    }

    $query .= " ORDER BY p.$order_by $order";

    if ($limit) {
        $query .= " LIMIT ?";
        $params[] = $limit;
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $posts]);
    exit;
}

// Create new post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = checkAuth();
    if (!$auth['success']) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['title']) || empty($data['content'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Title and content are required']);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            INSERT INTO posts (title, content, author_id, category_id) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['title'],
            $data['content'],
            $auth['user']['id'],
            $data['category_id'] ?? null
        ]);

        $post_id = $conn->lastInsertId();
        
        // Get the created post with author info
        $stmt = $conn->prepare("
            SELECT p.*, u.name as author_name, c.name as category_name 
            FROM posts p 
            LEFT JOIN users u ON p.author_id = u.id 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $post]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    exit;
}

// Update post
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $auth = checkAuth();
    if (!$auth['success']) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $post_id = $_GET['id'] ?? 0;
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if post exists and user is the author
    $stmt = $conn->prepare("SELECT author_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post || $post['author_id'] != $auth['user']['id']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            UPDATE posts 
            SET title = ?, content = ?, category_id = ? 
            WHERE id = ?
        ");
        
        $stmt->execute([
            $data['title'],
            $data['content'],
            $data['category_id'] ?? null,
            $post_id
        ]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    exit;
}

// Delete post
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $auth = checkAuth();
    if (!$auth['success']) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $post_id = $_GET['id'] ?? 0;

    // Check if post exists and user is the author
    $stmt = $conn->prepare("SELECT author_id FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post || $post['author_id'] != $auth['user']['id']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }

    try {
        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    exit;
}

try {
    // Get query parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $category = isset($_GET['category']) ? $_GET['category'] : null;
    $offset = ($page - 1) * $limit;

    // Build query
    $query = "
        SELECT p.*, u.name as author_name, c.name as category_name 
        FROM posts p 
        LEFT JOIN users u ON p.author_id = u.id 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.status = 'published'
    ";
    $params = [];

    if ($category) {
        $query .= " AND c.slug = ?";
        $params[] = $category;
    }

    $query .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;

    // Get posts
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) FROM posts p 
                   LEFT JOIN categories c ON p.category_id = c.id 
                   WHERE p.status = 'published'";
    if ($category) {
        $countQuery .= " AND c.slug = ?";
    }
    $stmt = $conn->prepare($countQuery);
    $stmt->execute($category ? [$category] : []);
    $total = $stmt->fetchColumn();

    // Return response
    echo json_encode([
        'success' => true,
        'data' => [
            'posts' => $posts,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ]
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 