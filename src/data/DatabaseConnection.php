<?php
namespace Data;

class DatabaseConnection {
    private static $instance = null;
    private $db;

    private function __construct() {
        $dbPath = __DIR__ . '/database/eduai.sqlite';
        
        // Tạo database file nếu chưa tồn tại
        if (!file_exists($dbPath)) {
            $this->db = new \SQLite3($dbPath);
            $this->initializeDatabase();
        } else {
            $this->db = new \SQLite3($dbPath);
        }
        
        $this->db->enableExceptions(true);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->db;
    }

    private function initializeDatabase() {
        // Tạo bảng users (người dùng)
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                full_name TEXT,
                role TEXT DEFAULT "student",
                avatar TEXT,
                bio TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');

        // Tạo bảng categories (danh mục khóa học)
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT,
                parent_id INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (parent_id) REFERENCES categories(id)
            )
        ');

        // Tạo bảng courses (khóa học)
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS courses (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category_id INTEGER,
                title TEXT NOT NULL,
                description TEXT,
                thumbnail TEXT,
                instructor_id INTEGER,
                price DECIMAL(10,2) DEFAULT 0,
                level TEXT DEFAULT "beginner",
                duration INTEGER, -- Thời lượng khóa học tính bằng phút
                status TEXT DEFAULT "draft",
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (instructor_id) REFERENCES users(id),
                FOREIGN KEY (category_id) REFERENCES categories(id)
            )
        ');

        // Tạo bảng lessons (bài học)
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS lessons (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                course_id INTEGER,
                title TEXT NOT NULL,
                content TEXT,
                video_url TEXT,
                duration INTEGER, -- Thời lượng bài học tính bằng phút
                order_index INTEGER,
                is_free BOOLEAN DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (course_id) REFERENCES courses(id)
            )
        ');

        // Tạo bảng enrollments (đăng ký khóa học)
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS enrollments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                course_id INTEGER,
                progress INTEGER DEFAULT 0,
                status TEXT DEFAULT "active",
                enrolled_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                completed_at DATETIME,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (course_id) REFERENCES courses(id)
            )
        ');

        // Tạo bảng lesson_progress (tiến độ học tập)
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS lesson_progress (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                lesson_id INTEGER,
                status TEXT DEFAULT "not_started",
                progress INTEGER DEFAULT 0,
                last_watched_position INTEGER DEFAULT 0,
                completed_at DATETIME,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (lesson_id) REFERENCES lessons(id)
            )
        ');

        // Tạo bảng quizzes (bài kiểm tra)
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS quizzes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                lesson_id INTEGER,
                title TEXT NOT NULL,
                description TEXT,
                duration INTEGER, -- Thời gian làm bài tính bằng phút
                pass_score INTEGER DEFAULT 70,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (lesson_id) REFERENCES lessons(id)
            )
        ');

        // Tạo bảng questions (câu hỏi)
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS questions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                quiz_id INTEGER,
                question_text TEXT NOT NULL,
                question_type TEXT DEFAULT "multiple_choice",
                points INTEGER DEFAULT 1,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
            )
        ');

        // Tạo bảng answers (câu trả lời)
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS answers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                question_id INTEGER,
                answer_text TEXT NOT NULL,
                is_correct BOOLEAN DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (question_id) REFERENCES questions(id)
            )
        ');

        // Tạo bảng quiz_attempts (lần làm bài kiểm tra)
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS quiz_attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                quiz_id INTEGER,
                score INTEGER,
                status TEXT DEFAULT "in_progress",
                started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                completed_at DATETIME,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
            )
        ');

        // Tạo bảng reviews (đánh giá khóa học)
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS reviews (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                course_id INTEGER,
                rating INTEGER CHECK (rating >= 1 AND rating <= 5),
                comment TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (course_id) REFERENCES courses(id)
            )
        ');

        // Tạo bảng certificates (chứng chỉ)
        $this->db->exec('
            CREATE TABLE IF NOT EXISTS certificates (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                course_id INTEGER,
                certificate_number TEXT UNIQUE,
                issued_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (course_id) REFERENCES courses(id)
            )
        ');

        // Tạo admin user mặc định
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $this->db->exec("
            INSERT OR IGNORE INTO users (username, email, password, role, full_name)
            VALUES ('admin', 'admin@eduai.com', '$adminPassword', 'admin', 'Administrator')
        ");

        // Tạo các danh mục khóa học mặc định
        $this->db->exec("
            INSERT OR IGNORE INTO categories (name, description) VALUES 
            ('Lập trình', 'Các khóa học về lập trình'),
            ('Ngoại ngữ', 'Các khóa học ngoại ngữ'),
            ('Kinh doanh', 'Các khóa học về kinh doanh'),
            ('Thiết kế', 'Các khóa học về thiết kế'),
            ('Marketing', 'Các khóa học về marketing')
        ");
    }
} 