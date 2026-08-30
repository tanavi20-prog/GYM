-- ============================================================================
-- CLEAN DATABASE SCHEMA FOR GYM MANAGEMENT SYSTEM
-- This matches exactly what your connect.php creates
-- Use this to get a fresh, clean database
-- ============================================================================

-- Drop database if exists (WARNING: This deletes all data!)
DROP DATABASE IF EXISTS fitness_db;

-- Create fresh database
CREATE DATABASE fitness_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fitness_db;

-- ============================================================================
-- CORE TABLES
-- ============================================================================

-- Users Table
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    age INT(3) DEFAULT NULL,
    gender ENUM('male', 'female', 'other') DEFAULT NULL,
    weight DECIMAL(5,2) DEFAULT NULL,
    height DECIMAL(5,2) DEFAULT NULL,
    fitnessgoal VARCHAR(50) DEFAULT NULL,
    dietarypreference VARCHAR(50) DEFAULT 'none',
    activitylevel VARCHAR(50) DEFAULT 'beginner',
    targetweight DECIMAL(5,2) DEFAULT NULL,
    createdat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Trainers Table
CREATE TABLE trainers (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(15) DEFAULT NULL,
    specialties JSON DEFAULT NULL,
    experience_years INT(2) DEFAULT 0,
    rating DECIMAL(2,1) DEFAULT 0.0,
    hourly_rate DECIMAL(6,2) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    certifications JSON DEFAULT NULL,
    available BOOLEAN DEFAULT TRUE,
    location VARCHAR(100) DEFAULT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_available (available)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Plans Table (combines workout and diet plans)
CREATE TABLE plans (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    type ENUM('workout', 'diet', 'combined') DEFAULT 'workout',
    difficulty ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    duration_weeks INT(2) DEFAULT NULL,
    price DECIMAL(8,2) DEFAULT NULL,
    features JSON DEFAULT NULL,
    created_by INT(11) DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_type (type),
    INDEX idx_difficulty (difficulty),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions Table (training sessions/bookings)
CREATE TABLE sessions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    trainer_id INT(11) DEFAULT NULL,
    plan_id INT(11) DEFAULT NULL,
    session_date DATETIME NOT NULL,
    duration_minutes INT(3) DEFAULT 60,
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    notes TEXT DEFAULT NULL,
    rating INT(1) DEFAULT NULL,
    feedback TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (trainer_id) REFERENCES trainers(id) ON DELETE SET NULL,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_trainer_id (trainer_id),
    INDEX idx_session_date (session_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Community Table (posts and discussions)
CREATE TABLE community (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('post', 'question', 'achievement', 'tip') DEFAULT 'post',
    category VARCHAR(50) DEFAULT NULL,
    likes_count INT(10) DEFAULT 0,
    comments_count INT(10) DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'hidden', 'reported') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Video Categories Table
CREATE TABLE video_categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(20) DEFAULT '🎬',
    color VARCHAR(7) DEFAULT '#22c55e',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Videos Table
CREATE TABLE videos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    youtube_url VARCHAR(255) NOT NULL,
    youtube_id VARCHAR(20) NOT NULL,
    thumbnail_url VARCHAR(500),
    duration_minutes INT(3) NOT NULL,
    difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    category_id INT(11),
    instructor_name VARCHAR(100),
    calories_per_minute DECIMAL(4,1) DEFAULT 5.0,
    views_count INT(10) DEFAULT 0,
    rating DECIMAL(2,1) DEFAULT 0.0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES video_categories(id) ON DELETE SET NULL,
    INDEX idx_difficulty (difficulty),
    INDEX idx_category_id (category_id),
    INDEX idx_active (is_active),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- SAMPLE DATA
-- ============================================================================

-- Insert Video Categories
INSERT INTO video_categories (name, description, icon, color) VALUES
('Cardio', 'Heart-pumping cardiovascular workouts', '❤️', '#ef4444'),
('Strength Training', 'Build muscle and strength', '💪', '#3b82f6'),
('Yoga', 'Flexibility and mindfulness', '🧘‍♀️', '#8b5cf6'),
('HIIT', 'High-intensity interval training', '🔥', '#f59e0b'),
('Pilates', 'Core strengthening and stability', '🤸‍♀️', '#ec4899'),
('Dance Fitness', 'Fun dance-based workouts', '💃', '#06d6a0'),
('Stretching', 'Flexibility and recovery', '🤲', '#22c55e'),
('Bodyweight', 'No equipment needed', '🏃‍♂️', '#84cc16');

-- Insert Sample Videos
INSERT INTO videos (title, description, youtube_url, youtube_id, thumbnail_url, duration_minutes, difficulty, category_id, instructor_name, calories_per_minute) VALUES
('10 Minute Morning Yoga Flow', 'Start your day with this energizing yoga sequence', 'https://www.youtube.com/watch?v=VaoV1PrYft4', 'VaoV1PrYft4', 'https://img.youtube.com/vi/VaoV1PrYft4/maxresdefault.jpg', 10, 'beginner', 3, 'Yoga with Adriene', 3.5),
('Full Body HIIT Workout - 20 Minutes', 'Intense full body workout to burn calories fast', 'https://www.youtube.com/watch?v=ml6cT4AZdqI', 'ml6cT4AZdqI', 'https://img.youtube.com/vi/ml6cT4AZdqI/maxresdefault.jpg', 20, 'intermediate', 4, 'FitnessBlender', 12.0),
('Beginner Strength Training - Arms', 'Perfect introduction to strength training for beginners', 'https://www.youtube.com/watch?v=gs1-7Yx-9d4', 'gs1-7Yx-9d4', 'https://img.youtube.com/vi/gs1-7Yx-9d4/maxresdefault.jpg', 15, 'beginner', 2, 'MadFit', 6.5),
('30 Minute Cardio Dance Workout', 'Fun dance workout to get your heart pumping', 'https://www.youtube.com/watch?v=gC_L9qAHVJ8', 'gC_L9qAHVJ8', 'https://img.youtube.com/vi/gC_L9qAHVJ8/maxresdefault.jpg', 30, 'intermediate', 6, 'The Fitness Marshall', 8.5),
('5 Minute Abs Workout', 'Quick and effective core strengthening routine', 'https://www.youtube.com/watch?v=MMB3zoK9pME', 'MMB3zoK9pME', 'https://img.youtube.com/vi/MMB3zoK9pME/maxresdefault.jpg', 5, 'beginner', 2, 'Athlean-X', 7.0),
('Pilates Full Body Workout', 'Complete pilates session for strength and flexibility', 'https://www.youtube.com/watch?v=K56Z12GJF88', 'K56Z12GJF88', 'https://img.youtube.com/vi/K56Z12GJF88/maxresdefault.jpg', 25, 'intermediate', 5, 'Blogilates', 4.5),
('Morning Stretch Routine', 'Gentle stretches to wake up your body', 'https://www.youtube.com/watch?v=g_tea8ZNk5A', 'g_tea8ZNk5A', 'https://img.youtube.com/vi/g_tea8ZNk5A/maxresdefault.jpg', 12, 'beginner', 7, 'Yoga with Tim', 2.0),
('No Equipment Full Body Workout', 'Complete bodyweight workout you can do anywhere', 'https://www.youtube.com/watch?v=UBMk30rjy0o', 'UBMk30rjy0o', 'https://img.youtube.com/vi/UBMk30rjy0o/maxresdefault.jpg', 22, 'intermediate', 8, 'Calisthenic Movement', 9.0),
('Advanced Cardio HIIT Challenge', 'High-intensity workout for experienced fitness enthusiasts', 'https://www.youtube.com/watch?v=3spdFM_4U9U', '3spdFM_4U9U', 'https://img.youtube.com/vi/3spdFM_4U9U/maxresdefault.jpg', 18, 'advanced', 4, 'HASfit', 14.0),
('Relaxing Evening Yoga', 'Wind down with this calming yoga session', 'https://www.youtube.com/watch?v=BiWDsfZ3I2w', 'BiWDsfZ3I2w', 'https://img.youtube.com/vi/BiWDsfZ3I2w/maxresdefault.jpg', 20, 'beginner', 3, 'Boho Beautiful', 3.0);

-- Insert Sample Trainers (Optional - remove if you want to add manually)
INSERT INTO trainers (name, email, phone, specialties, experience_years, rating, hourly_rate, bio, certifications, available, location) VALUES
('Sarah Johnson', 'sarah@fittrainer.com', '+1234567890', '["Strength Training", "Weight Loss", "HIIT"]', 5, 4.9, 75.00, 'Certified personal trainer specializing in strength training and weight loss programs.', '["NASM-CPT", "HIIT Specialist"]', TRUE, 'New York'),
('Mike Rodriguez', 'mike@fittrainer.com', '+1234567891', '["Bodybuilding", "Nutrition", "Powerlifting"]', 8, 4.8, 85.00, 'Bodybuilding expert with over 8 years of experience in muscle building and nutrition.', '["ISSA-CPT", "Nutrition Coach"]', TRUE, 'Los Angeles'),
('Emma Chen', 'emma@fittrainer.com', '+1234567892', '["Yoga", "Flexibility", "Mindfulness"]', 6, 4.9, 65.00, 'Yoga instructor and mindfulness coach focusing on flexibility and mental wellness.', '["RYT-500", "Mindfulness Coach"]', TRUE, 'San Francisco'),
('Alex Thompson', 'alex@fittrainer.com', '+1234567893', '["CrossFit", "Endurance", "Athletic Performance"]', 4, 4.7, 80.00, 'CrossFit athlete and endurance specialist helping clients achieve peak performance.', '["CrossFit Level 2", "Endurance Coach"]', TRUE, 'Chicago');

-- ============================================================================
-- COMPLETION MESSAGE
-- ============================================================================
SELECT 'Database created successfully!' as Status,
       (SELECT COUNT(*) FROM video_categories) as Categories,
       (SELECT COUNT(*) FROM videos) as Videos,
       (SELECT COUNT(*) FROM trainers) as Trainers;
