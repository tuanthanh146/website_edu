<?php
require_once __DIR__ . '/../../bootstrap.php';

// Kiểm tra đăng nhập và quyền admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tableName = $_POST['table'] ?? '';
    $id = $_POST['id'] ?? '';
    
    if (empty($tableName) || empty($id)) {
        die('Table name and ID are required');
    }

    // Lấy thông tin database
    $db = \Data\DatabaseConnection::getInstance()->getConnection();

    // Tạo câu lệnh DELETE
    $stmt = $db->prepare("
        DELETE FROM " . SQLite3::escapeString($tableName) . "
        WHERE id = :id
    ");

    // Bind ID
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);

    try {
        $stmt->execute();
        header("Location: ../table.php?name=" . urlencode($tableName) . "&success=1");
    } catch (\Exception $e) {
        header("Location: ../table.php?name=" . urlencode($tableName) . "&error=" . urlencode($e->getMessage()));
    }
    exit;
}

header('Location: ../index.php');
exit; 