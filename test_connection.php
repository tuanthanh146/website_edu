<?php
require_once 'includes/config.php';

try {
    // Test query
    $sql = "SELECT 1 as test";
    $stmt = $conn->query($sql);
    $result = $stmt->fetch();
    
    if ($result) {
        echo "Database connection successful!";
    } else {
        echo "Database connection failed!";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 