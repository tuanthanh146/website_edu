-- Create AI interactions table
CREATE TABLE IF NOT EXISTS ai_interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    question TEXT NOT NULL,
    response TEXT NOT NULL,
    model_used VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create AI training data table
CREATE TABLE IF NOT EXISTS ai_training_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(50),
    source ENUM('user', 'admin', 'system') DEFAULT 'user',
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create AI model versions table
CREATE TABLE IF NOT EXISTS ai_model_versions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_name VARCHAR(50) NOT NULL,
    version VARCHAR(20) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create AI Tutor table
CREATE TABLE IF NOT EXISTS ai_tutor_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(50) NOT NULL,
    topic VARCHAR(100) NOT NULL,
    difficulty_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create AI Tutor Progress table
CREATE TABLE IF NOT EXISTS ai_tutor_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    concept_covered TEXT NOT NULL,
    understanding_level ENUM('poor', 'fair', 'good', 'excellent') DEFAULT 'fair',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES ai_tutor_sessions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create AI Auto Training table
CREATE TABLE IF NOT EXISTS ai_auto_training (
    id INT AUTO_INCREMENT PRIMARY KEY,
    interaction_id INT NOT NULL,
    question TEXT NOT NULL,
    response TEXT NOT NULL,
    user_feedback ENUM('positive', 'negative', 'neutral') DEFAULT 'neutral',
    confidence_score FLOAT DEFAULT 0,
    training_status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (interaction_id) REFERENCES ai_interactions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create AI Training Metrics table
CREATE TABLE IF NOT EXISTS ai_training_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_version VARCHAR(20) NOT NULL,
    accuracy FLOAT DEFAULT 0,
    precision FLOAT DEFAULT 0,
    recall FLOAT DEFAULT 0,
    f1_score FLOAT DEFAULT 0,
    training_samples INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default model version
INSERT INTO ai_model_versions (model_name, version) VALUES 
('deepseek-chat', '1.0'); 


CREATE TABLE IF NOT EXISTS ai_3d_sessions {
    id TNT AUTO_INCREMENT PRIMARY KEY,
    user_id TNT NOT NULL ,
    subject VARCHAR(50) NOT NULL ,
    topic VARCHAR(100) NOT NULL ,
    difficulty_level VARCHAR(20) NOT NULL , 
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP , 
    end_time TIMESTAMP NULL , 
    status VARCHAR(20) DEFAULT 'active' , 
    FOREIGN KEY (user_id) REFERNECES users(id)  
};

CREATE TABLE IF NOT EXISTS ai_3d_interactions {
    id TNT AUTO_INCREMENT PRIMARY KEY , 
    session_id TNT NOT NULL , 
    message TEXT NOT NULL , 
    is_voice BOOLEAN DEFAULT FALSE , 
    created_at TIMESTAMP DEFAUTL CURRENT_TIMESTAMP , 
    FOREIGN KEY (session_id) REFERENCES ai_3d_sessions(id)
}

CREATE TABLE IF NOT EXISTS ai_3d_responses { 
    id TNT AUTO_INCREMENT PRIMARY KEY , 
    session_id TNT NOT NULL , 
    response TEXT NOT NULL , 
    created_at TIMESTAMP DEFAULT CURRNET_TIMESTAMP , 
    FOREIGN KEY (session_id) REFERENCES ai_3d_sessions(id)
};
