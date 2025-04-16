<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get categories for dropdown
$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng bài viết mới - EduAI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">Đăng bài viết mới</h3>
                    </div>
                    <div class="card-body">
                        <form id="postForm" method="POST">
                            <div class="mb-3">
                                <label for="title" class="form-label">Tiêu đề</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Danh mục</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Chọn danh mục</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Nội dung</label>
                                <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">Đăng bài</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/posts.js"></script>
</body>
</html> 