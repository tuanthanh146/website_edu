<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

try {
    $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'categories' => $categories
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 