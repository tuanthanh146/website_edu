<?php
require_once __DIR__ . '/../../../bootstrap.php';

// Kiểm tra đăng nhập và quyền admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tableName = $_POST['table'] ?? '';
    if (empty($tableName)) {
        die('Table name is required');
    }

    // Lấy thông tin database
    $db = \Data\DatabaseConnection::getInstance()->getConnection();

    // Lấy thông tin cột
    $columns = [];
    $result = $db->query("PRAGMA table_info(" . SQLite3::escapeString($tableName) . ")");
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        if ($row['name'] !== 'id') {
            $columns[] = $row['name'];
        }
    }

    // Tạo câu lệnh INSERT
    $fields = implode(', ', $columns);
    $placeholders = implode(', ', array_map(function($col) { return ":$col"; }, $columns));
    
    $stmt = $db->prepare("
        INSERT INTO " . SQLite3::escapeString($tableName) . " 
        ($fields) VALUES ($placeholders)
    ");

    // Bind các giá trị
    foreach ($columns as $column) {
        $value = $_POST[$column] ?? '';
        $stmt->bindValue(":$column", $value);
    }

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