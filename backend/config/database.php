<?php
require_once __DIR__ . '/../../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

try {
    // Database configuration
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $dbname = $_ENV['DB_NAME'] ?? 'eduai_blog';
    $username = $_ENV['DB_USER'] ?? 'root';
    $password = $_ENV['DB_PASS'] ?? '';

    // Create PDO connection
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage());
} 