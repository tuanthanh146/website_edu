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
    $id = $_POST['id'] ?? '';
    
    if (empty($tableName) || empty($id)) {
        die('Table name and ID are required');
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

    // Tạo câu lệnh UPDATE
    $setClause = implode(', ', array_map(function($col) { return "$col = :$col"; }, $columns));
    
    $stmt = $db->prepare("
        UPDATE " . SQLite3::escapeString($tableName) . "
        SET $setClause
        WHERE id = :id
    ");

    // Bind các giá trị
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
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