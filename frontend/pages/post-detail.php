<?php
require_once '../config/database.php';

$post_id = $_GET['id'] ?? 0;

// Get post details with author and category information
$stmt = $conn->prepare("
    SELECT p.*, u.name as author_name, c.name as category_name 
    FROM posts p 
    LEFT JOIN users u ON p.author_id = u.id 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ?
");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: posts.php');
    exit();
}

// Increment view count
$conn->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$post_id]);

// Get comments for this post
$stmt = $conn->prepare("
    SELECT c.*, u.name as user_name 
    FROM comments c 
    LEFT JOIN users u ON c.user_id = u.id 
    WHERE c.post_id = ? 
    ORDER BY c.created_at DESC
");
$stmt->execute([$post_id]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle comment submission
$comment_error = '';
$comment_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $content = $_POST['content'] ?? '';
    
    if (empty($content)) {
        $comment_error = 'Vui lòng nhập nội dung bình luận';
    } else {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
        if ($stmt->execute([$post_id, $_SESSION['user_id'], $content])) {
            $comment_success = 'Bình luận đã được đăng thành công!';
            header("Location: post-detail.php?id=$post_id");
            exit();
        } else {
            $comment_error = 'Có lỗi xảy ra khi đăng bình luận';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - EduAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <div class="col-md-8">
                <article>
                    <h1 class="mb-4"><?php echo htmlspecialchars($post['title']); ?></h1>
                    <div class="text-muted mb-4">
                        <p>
                            Đăng bởi <?php echo htmlspecialchars($post['author_name']); ?> 
                            vào <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                            <?php if ($post['category_name']): ?>
                                | Danh mục: <?php echo htmlspecialchars($post['category_name']); ?>
                            <?php endif; ?>
                            | Lượt xem: <?php echo $post['views']; ?>
                        </p>
                    </div>
                    <div class="content">
                        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                    </div>
                </article>

                <section class="mt-5">
                    <h3>Bình luận</h3>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <?php if ($comment_error): ?>
                                    <div class="alert alert-danger"><?php echo $comment_error; ?></div>
                                <?php endif; ?>
                                <?php if ($comment_success): ?>
                                    <div class="alert alert-success"><?php echo $comment_success; ?></div>
                                <?php endif; ?>

                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Bình luận của bạn</label>
                                        <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Gửi bình luận</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Vui lòng <a href="login.php">đăng nhập</a> để bình luận.
                        </div>
                    <?php endif; ?>

                    <?php if (count($comments) > 0): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($comment['user_name']); ?></h5>
                                    <p class="text-muted">
                                        <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>
                                    </p>
                                    <p class="card-text"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">Chưa có bình luận nào.</div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 